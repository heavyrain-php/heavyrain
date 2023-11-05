<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Console\Commands;

use cebe\openapi\Reader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Generates ApiClient file from OpenAPI specification
 */
#[AsCommand(
    name: 'generate:client',
    description: 'Generates ApiClient file from OpenAPI specification',
)]
final class GenerateClientCommand extends Command
{
    protected function configure(): void
    {
        // TODO: stdin/stdout support
        $this
            ->addArgument(
                'openapi',
                InputArgument::REQUIRED,
                'Root openapi.yaml/json filename',
            )->addArgument(
                'output',
                InputArgument::REQUIRED,
                'Output filename',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $io->title(\sprintf('Heavyrain Loadtest generate:client version:%s', $this->getApplication()?->getVersion() ?? 'dev'));

        $openapiFilename = $input->getArgument('openapi');
        \assert(\is_string($openapiFilename));
        $outputFilename = $input->getArgument('output');
        \assert(\is_string($outputFilename));

        $openApi = match (\pathinfo($openapiFilename, \PATHINFO_EXTENSION)) {
            'json' => Reader::readFromJsonFile($openapiFilename),
            'yaml', 'yml' => Reader::readFromYamlFile($openapiFilename),
            default => throw new \RuntimeException(\sprintf('File "%s" has unsupported extension. Supported: json, yaml, yml', $openapiFilename)),
        };

        $io->text(\sprintf('Generating ApiClient file "%s" from "%s"', $outputFilename, $openapiFilename));

        return Command::SUCCESS;
    }
}
