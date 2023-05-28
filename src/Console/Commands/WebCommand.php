<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;

#[AsCommand(
    name: 'web',
    description: 'Start to serve web console server',
)]
final class WebCommand extends Command
{
    protected function configure(): void
    {
        $this
            ->addOption('host', null, InputOption::VALUE_OPTIONAL, 'serve hostname', 'localhost')
            ->addOption('port', null, InputOption::VALUE_OPTIONAL, 'serve port', 8080);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $host = $input->getOption('host');
        \assert(\is_string($host));
        $port = $input->getOption('port');
        \assert(\is_numeric($port));

        $php = (new PhpExecutableFinder())->find(false);

        $process = new Process(
            command: [$php, '-S', \sprintf('%s:%d', $host, $port), '-t', 'public'],
            timeout: null,
        );

        $process->run(static function (string $type, string $buffer) use ($output) {
            if ($type === Process::ERR) {
                \assert($output instanceof ConsoleOutputInterface);
                $output->getErrorOutput()->write($buffer);
            } else {
                $output->write($buffer);
            }
        });

        return Command::SUCCESS;
    }
}
