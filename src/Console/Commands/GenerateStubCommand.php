<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'generate:stub',
    description: 'Generates stub interface file for static analysis',
)]
final class GenerateStubCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // @todo implementation
        throw new \LogicException('Not implemented');
    }
}
