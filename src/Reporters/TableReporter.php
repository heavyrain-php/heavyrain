<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Reporters;

use Heavyrain\Contracts\HttpProfilerInterface;
use Heavyrain\Contracts\HttpResultInterface;
use Heavyrain\Contracts\ReporterInterface;
use Heavyrain\Scenario\RequestException;
use Heavyrain\Scenario\ResponseAssertionException;
use Symfony\Component\Console\Style\SymfonyStyle;
use Throwable;

class TableReporter implements ReporterInterface
{
    public function __construct(private readonly SymfonyStyle $io)
    {
    }

    public function report(HttpProfilerInterface $profiler): void
    {
        $table = $this->io->createTable();
        $rows = \array_map(
            fn (HttpResultInterface $result): array => $this->getProfileRow($result),
            $profiler->getResults(),
        );
        $table
            ->setHeaders(['Method', 'Path', 'Total(ms)'])
            ->addRows($rows)
            ->render();

        $table = $this->io->createTable();
        $rows = \array_map(
            fn (Throwable $exception): array => $this->getExceptionRow($exception),
            $profiler->getExceptions(),
        );
        if (\count($rows) > 0) {
            $this->io->error('Some requests has Error');
            $table
                ->setHeaders(['Class', 'Path', 'Message', 'Body'])
                ->addRows($rows)
                ->render();
        }
    }

    private function getProfileRow(HttpResultInterface $result): array
    {
        $request = $result->getRequest();
        $curlInfo = $result->getCurlInfo();

        return [
            $request['method'],
            $request['path'],
            \is_null($curlInfo) ? 0 : \round(\intval($curlInfo['total_time_us']) / 10) / 100,
        ];
    }

    private function getExceptionRow(Throwable $exception): array
    {
        if ($exception instanceof RequestException) {
            return [
                'RequestException',
                $exception->getRequest()->getUri()->__toString(),
                $exception->getMessage(),
                $exception->getResponse()?->getBody()->__toString(),
            ];
        }
        if ($exception instanceof ResponseAssertionException) {
            return [
                'ResponseAssertionException',
                $exception->getResponse()->getHeaderLine('Host'),
                $exception->getMessage(),
                $exception->getResponse()->getBody()->__toString(),
            ];
        }
        return [
            \get_class($exception),
            '',
            $exception->getMessage(),
            '',
        ];
    }
}
