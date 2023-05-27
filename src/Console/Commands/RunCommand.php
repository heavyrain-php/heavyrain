<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use Closure;
use GuzzleHttp\Client;
use GuzzleHttp\TransferStats;
use Heavyrain\Scenario\Instructors\GuzzleInstructorFactory;
use ReflectionFunction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'run',
    description: 'Run scenario',
)]
final class RunCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addArgument(
                'scenario-php-file',
                InputArgument::REQUIRED,
                'PHP filename for scenario',
            )->addOption(
                'base-uri',
                'b',
                InputOption::VALUE_OPTIONAL,
                'Request base URI',
                'http://localhost',
            )->addOption(
                'timeout',
                't',
                InputOption::VALUE_OPTIONAL,
                'Request timeout seconds',
                0.0,
            )->addOption(
                'connect-timeout',
                'c',
                InputOption::VALUE_OPTIONAL,
                'Request connection timeout seconds',
                0,
            )->addOption(
                'read-timeout',
                'r',
                InputOption::VALUE_OPTIONAL,
                'Response read timeout',
                null,
            )->addOption(
                'no-verify',
                's',
                InputOption::VALUE_OPTIONAL,
                'Disables SSL certificate verification',
                false,
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = \getcwd();
        assert($cwd !== false);

        /** @var string $baseUri */
        $baseUri = $input->getOption('base-uri');
        $timeout = floatval($input->getOption('timeout'));
        $connectTimeout = floatval($input->getOption('connect-timeout'));
        $readTimeout = floatval($input->getOption('read-timeout') ?? ini_get('default_socket_timeout'));
        $noVerify = boolval($input->getOption('no-verify'));

        /** @var string $scenarioFileName */
        $scenarioFileName = $input->getArgument('scenario-php-file');
        $scenarioFilePath = sprintf('%s/%s', $cwd, $scenarioFileName);
        $io = new SymfonyStyle($input, $output);

        if (!\str_ends_with($scenarioFilePath, '.php') || !\file_exists($scenarioFilePath)) {
            $io->error(\sprintf('%s file not found', $scenarioFilePath));
            return Command::INVALID;
        }
        assert(\file_exists($scenarioFilePath)); // for psalm

        /** @var mixed $func */
        $func = require $scenarioFilePath;
        if (!$func instanceof Closure) {
            $io->error('scenario-php-file must return Closure');
            return Command::INVALID;
        }
        $ref = new ReflectionFunction($func);
        if (!$ref->isStatic()) {
            $io->warning('Closure should be static: `return static function(...`');
        }

        $client = new Client([
            'allow_redirects' => false,
            'base_uri' => $baseUri,
            'connect_timeout' => $connectTimeout,
            'on_stats' => static function (TransferStats $stats) use ($io): void {
                $table = $io->createTable();
                $rows = [];
                /** @var scalar|scalar[] $value */
                foreach ($stats->getHandlerStats() as $key => $value) {
                    $rows[] = [$key, is_array($value) ? implode(', ', $value) : $value];
                }
                $table->addRows($rows);
                $table->render();
            },
            'read_timeout' => $readTimeout,
            'verify' => !$noVerify,
            'timeout' => $timeout,
        ]);
        $inst = GuzzleInstructorFactory::create($client);

        $ref->invoke($inst);

        return Command::SUCCESS;
    }
}
