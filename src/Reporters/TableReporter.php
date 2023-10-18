<?php

/**
 * @license MIT
 */

declare(strict_types=1);

namespace Heavyrain\Reporters;

use Heavyrain\Contracts\ReporterInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class TableReporter implements ReporterInterface
{
    public function __construct(private readonly SymfonyStyle $io)
    {
    }

    public function report(array $results): void
    {
        $rows = [];
        foreach ($results as $result) {
            $rows[] = [
                'request' => \json_encode($result['request']),
                'response' => \json_encode($result['response']),
                'requestException' => \json_encode($result['requestException']),
                'uncaughtException' => \json_encode($result['uncaughtException']),
            ];
        }
        $table = $this->io->createTable();
        $table
            ->addRows($rows)
            ->render();

        // TODO: remove
        // $rows = [];
        // foreach ($profiler->getResults() as $path => $results) {
        //     $pathInfo = \explode('-', $path, 2);
        //     \assert(\count($pathInfo) === 2);
        //     $rows[] = [...$pathInfo, ...$this->getProfileRow($results)];
        // }
        // $table
        //     ->setHeaders(['Method', 'Path', 'Count', 'Min', 'Max', 'Median', 'Average'])
        //     ->addRows($rows)
        //     ->render();

        // $table = $this->io->createTable();
        // $rows = \array_map(
        //     fn (Throwable $exception): array => $this->getExceptionRow($exception),
        //     $profiler->getExceptions(),
        // );
        // if (\count($rows) > 0) {
        //     $this->io->error('Some requests has Error');
        //     $table
        //         ->setHeaders(['Class', 'Path', 'Message', 'Body'])
        //         ->addRows($rows)
        //         ->render();
        // }
    }

    // private function getProfileRow(array $results): array
    // {
    //     $totalTimeList = array_map(
    //         function ($result): float {
    //             $curlInfo = $result->getCurlInfo();
    //             if (\is_null($curlInfo)) {
    //                 return 0.0;
    //             }
    //             return \round(\intval($curlInfo['total_time_us']) / 10) / 100;
    //         },
    //         $results,
    //     );

    //     if (\count($totalTimeList) === 0) {
    //         return [0, 0.0, 0.0, 0.0, 0.0];
    //     }

    //     \sort($totalTimeList, \SORT_NUMERIC | \SORT_ASC);

    //     return [
    //         \count($totalTimeList),
    //         $totalTimeList[0],
    //         $totalTimeList[\count($totalTimeList) - 1],
    //         $totalTimeList[\count($totalTimeList) / 2],
    //         \array_sum($totalTimeList) / \count($totalTimeList),
    //     ];
    // }

    // private function getExceptionRow(Throwable $exception): array
    // {
    //     if ($exception instanceof RequestException) {
    //         return [
    //             'RequestException',
    //             $exception->getRequest()->getUri()->__toString(),
    //             $exception->getMessage(),
    //             $exception->getResponse()?->getBody()->__toString(),
    //         ];
    //     }
    //     if ($exception instanceof ResponseAssertionException) {
    //         return [
    //             'ResponseAssertionException',
    //             $exception->getRequest()->getUri()->__toString(),
    //             $exception->getMessage(),
    //             $exception->getResponse()->getBody()->__toString(),
    //         ];
    //     }
    //     return [
    //         \get_class($exception),
    //         '',
    //         $exception->getMessage(),
    //         '',
    //     ];
    // }
}
