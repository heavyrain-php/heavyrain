<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use Closure;
use Heavyrain\Executor\ExecutorConfig;
use Heavyrain\Executor\SyncExecutor;
use Heavyrain\Reporters\TableReporter;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\RequestException;
use Heavyrain\Scenario\ResponseAssertionException;
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
            )->addArgument(
                'base-uri',
                InputArgument::REQUIRED,
                'Request base URI',
            )->addOption(
                'timeout',
                null,
                InputOption::VALUE_REQUIRED,
                'Request timeout seconds',
                0.0,
            )->addOption(
                'no-verify',
                null,
                InputOption::VALUE_NONE,
                'Disables SSL certificate verification',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $cwd = \getcwd();
        assert($cwd !== false);

        /** @var string $baseUri */
        $baseUri = $input->getArgument('base-uri');
        $timeout = floatval($input->getOption('timeout'));
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
        $scenarioFunction = new ReflectionFunction($func);
        if (\method_exists($scenarioFunction, 'isStatic') && !$scenarioFunction->isStatic()) {
            $io->warning('Closure should be static: `return static function(...`');
        }

        $config = new ExecutorConfig(
            $baseUri,
            !$noVerify,
            $timeout,
        );
        $profiler = new HttpProfiler();
        $executor = new SyncExecutor($config, $scenarioFunction, $profiler);

        // TODO: concurrency
        $executor->execute();

        (new TableReporter($io))->report($profiler);

        return Command::SUCCESS;
    }
}
