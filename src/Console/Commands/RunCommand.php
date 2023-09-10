<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use Closure;
use Heavyrain\Executor\ExecutorConfig;
use Heavyrain\Executor\ExecutorFactory;
use Heavyrain\Executor\SyncExecutor;
use Heavyrain\HttpClient\ClientFactory;
use Heavyrain\HttpClient\HttpProfiler;
use Heavyrain\Reporters\TableReporter;
use Heavyrain\Scenario\CancellationToken;
use ReflectionFunction;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\SignalableCommandInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'run:single',
    description: 'Run scenario once',
)]
final class RunCommand extends Command implements SignalableCommandInterface
{
    private ?CancellationToken $cancelToken = null;

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
                'output',
                'o',
                InputOption::VALUE_REQUIRED,
                'Output format',
                'table',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(\sprintf('Heavyrain Loadtest runner version:%s', $this->getApplication()?->getVersion() ?? 'dev'));

        $cwd = \getcwd();
        \assert($cwd !== false);

        /** @var string $baseUri */
        $baseUri = $input->getArgument('base-uri');

        /** @var string */
        $scenarioFileName = $input->getArgument('scenario-php-file');
        $scenarioFilePath = \sprintf('%s/%s', $cwd, $scenarioFileName);

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
            $io->warning('Scenario Closure should be static: `return static function(...`');
        }
        $this->cancelToken = new CancellationToken();

        $io->definitionList(
            ['Base URI' => $baseUri],
            ['Scenario' => $scenarioFilePath],
        );

        $startMicrosec = \microtime(true);
        $io->writeln(\sprintf('Start execution at %s', \date('Y-m-d H:i:s')));
        $profiler = new HttpProfiler();

        (new SyncExecutor($scenarioFunction->getClosure(), new ClientFactory($profiler, $baseUri)))
            ->execute($this->cancelToken);

        $io->writeln(
            \sprintf(
                'End execution   at %s (%.3f seconds)',
                \date('Y-m-d H:i:s'),
                \microtime(true) - $startMicrosec,
            ),
        );

        $reporter = match ($input->getOption('output')) {
            'table' => new TableReporter($io),
            default => new TableReporter($io),
        };
        $reporter->report($profiler->getResults());

        return Command::SUCCESS;
    }

    public function getSubscribedSignals(): array
    {
        return [\SIGINT, \SIGTERM];
    }

    public function handleSignal(int $signal)
    {
        $this->cancelToken?->cancel();
        return false;
    }
}
