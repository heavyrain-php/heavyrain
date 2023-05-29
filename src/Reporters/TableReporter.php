<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Reporters;

use Heavyrain\Scenario\HttpProfiler;
use Heavyrain\Scenario\RequestException;
use Heavyrain\Scenario\ResponseAssertionException;
use Symfony\Component\Console\Style\SymfonyStyle;

class TableReporter
{
    public function __construct(private readonly SymfonyStyle $io)
    {
    }

    public function report(HttpProfiler $profiler): void
    {
        // TODO: to Reporter class
        $table = $this->io->createTable();
        $rows = [];
        foreach ($profiler->getResults() as $profile) {
            if (!$profile->isSucceeded()) {
                continue;
            }
            $rows[] = [
                \sprintf('%s %s', $profile->request['method'], $profile->request['path']),
                \is_null($profile->curlInfo) ? 0 : \round(\intval($profile->curlInfo['total_time_us']) / 10) / 100,
                $profile->isSucceeded(),
            ];
        }
        $table
            ->setHeaders(['Path', 'Total(ms)', 'isSucceeded'])
            ->addRows($rows)
            ->render();

        $table = $this->io->createTable();
        $rows = [];
        foreach ($profiler->getExceptions() as $exception) {
            if ($exception instanceof RequestException) {
                $rows[] = [
                    'RequestException',
                    $exception->getRequest()->getUri()->__toString(),
                    $exception->getMessage(),
                    $exception->getResponse()?->getBody()->__toString(),
                ];
                continue;
            } elseif ($exception instanceof ResponseAssertionException) {
                $rows[] = [
                    'ResponseAssertionException',
                    $exception->getResponse()->getHeaderLine('Host'),
                    $exception->getMessage(),
                    $exception->getResponse()->getBody()->__toString(),
                ];
                continue;
            }
            $rows[] = [
                \get_class($exception),
                $exception->getMessage(),
                '',
            ];
        }
        if (\count($rows) > 0) {
            $this->io->error('Some requests has Error');
            $table
                ->setHeaders(['Class', 'Path', 'Message', 'Body'])
                ->addRows($rows)
                ->render();
        }
    }
}
