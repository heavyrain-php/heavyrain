<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use Closure;
use Heavyrain\Executor\ExecutorConfig;
use Heavyrain\Executor\ExecutorFactory;
use Heavyrain\Reporters\TableReporter;
use Heavyrain\Scenario\CancellationToken;
use Heavyrain\Scenario\DefaultScenarioConfig;
use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\ScenarioConfigInterface;
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
    name: 'run',
    description: 'Run scenario',
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
                'config',
                'c',
                InputOption::VALUE_REQUIRED,
                'PHP filename for scenario config',
            )->addOption(
                'timeout',
                't',
                InputOption::VALUE_REQUIRED,
                'Request timeout seconds',
                0.0,
            )->addOption(
                'verify-cert',
                null,
                InputOption::VALUE_NONE,
                'Enables SSL/TLS certificate verification',
            )->addOption(
                'wait-after-scenario',
                null,
                InputOption::VALUE_REQUIRED,
                'Wait seconds after scenario',
                1.0,
            )->addOption(
                'wait-after-request',
                null,
                InputOption::VALUE_REQUIRED,
                'Wait seconds after request',
                0.3,
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
        $timeout = \floatval($input->getOption('timeout'));
        $verifyCert = \boolval($input->getOption('verify-cert'));
        $waitAfterScenarioSec = \floatval($input->getOption('wait-after-scenario'));
        $waitAfterSendRequestSec = \floatval($input->getOption('wait-after-request'));

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

        /** @var ScenarioConfigInterface */
        $scenarioConfig = new DefaultScenarioConfig();
        /** @var ?string $scenarioConfigFileName */
        $scenarioConfigFileName = $input->getOption('config');
        if (!\is_null($scenarioConfigFileName)) {
            $scenarioConfigFilePath = \sprintf('%s/%s', $cwd, $scenarioConfigFileName);
            if (!\str_ends_with($scenarioConfigFilePath, '.php') || !\file_exists($scenarioConfigFilePath)) {
                $io->error(\sprintf('%s file not found', $scenarioConfigFilePath));
                return Command::INVALID;
            }
            assert(\file_exists($scenarioConfigFilePath)); // for psalm

            /** @var mixed */
            $scenarioConfigFunc = require $scenarioConfigFilePath;
            if (!$scenarioConfigFunc instanceof Closure) {
                $io->error('config must return Closure');
                return Command::INVALID;
            }
            $scenarioConfigReflection = new ReflectionFunction($scenarioConfigFunc);
            if (\method_exists($scenarioConfigReflection, 'isStatic') && !$scenarioConfigReflection->isStatic()) {
                $io->warning('Config Closure should be static: `return static function(...`');
            }
            /** @var mixed */
            $scenarioConfig = $scenarioConfigReflection->invoke();
            if (!$scenarioConfig instanceof ScenarioConfigInterface) {
                $io->error('Config Closure must return Heavyrain\Scenario\ScenarioConfigInterface');
                return Command::INVALID;
            }
        }

        $userAgentBase = \sprintf(
            '%s/%s',
            $this->getApplication()?->getName() ?? 'heavyrain',
            $this->getApplication()?->getVersion() ?? 'dev',
        );
        $config = new ExecutorConfig(
            $baseUri,
            $scenarioConfig,
            $userAgentBase,
            $waitAfterScenarioSec,
            $waitAfterSendRequestSec,
            $verifyCert,
            $timeout,
        );
        $profiler = new HttpProfiler();
        $this->cancelToken = new CancellationToken();

        $io->definitionList(
            ['Base URI' => $baseUri],
            ['Scenario' => $scenarioFilePath],
            ['Scenario config' => $scenarioConfigFilePath ?? 'DefaultScenarioConfig'],
            ['SSL/TLS verify' => $verifyCert ? 'yes' : 'no (default)'],
        );

        $startMicrosec = \microtime(true);
        $io->writeln(\sprintf('Start execution at %s', \date('Y-m-d H:i:s')));

        // TODO: Select executor
        (new ExecutorFactory($config, $scenarioFunction->getClosure(), $profiler))
            ->createSync()
            ->execute($this->cancelToken);

        $io->writeln(
            \sprintf(
                'End execution   at %s (%f seconds)',
                \date('Y-m-d H:i:s'),
                \microtime(true) - $startMicrosec,
            ),
        );

        // TODO: Select reporter
        (new TableReporter($io))->report($profiler);

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
