# HTTP Client benchmark

## Usage

```sh
$ composer install
$ composer bench
```

## Target list

### PSR-7 implementations

- [guzzlehttp/psr7](https://packagist.org/pakcages/guzzlehttp/psr7)
- [nyholm/psr7](https://packagist.org/pakcages/nyholm/psr7)
- [laminas/laminas-diactoros](https://packagist.org/pakcages/laminas/laminas-diactoros)

### PSR-7 compatible clients

- [guzzlehttp/guzzle](https://packagist.org/packages/guzzlehttp/guzzle)
    - `CurlHandler`
    - `CurlMultiHandler`
    - `StreamHandler`
- [kriswallsmith/buzz](https://packagist.org/packages/kriswallsmith/buzz)
    - `Curl`
    - `FileGetContents`
    - `MultiCurl`
- [react/http](https://packagist.org/packages/react/http)

### Not PSR-7 compatible clients

- [amphp/http-client](https://github.com/amphp/http-client)
- [laminas/laminas-http](https://packagist.org/packages/laminas/laminas-http)
- [symfony/http-kernel](https://packagist.org/packages/symfony/http-kernel)

## Result

```
PHPBench (1.2.10) running benchmarks... #standwithukraine
with PHP version 8.2.6, xdebug ❌, opcache ✔

...........................................

Subjects: 6, Assertions: 0, Failures: 0, Errors: 0
```

| benchmark       | subject      | set                                 | revs | its | mem_peak | mode     | rstdev |
|-----------------|--------------|-------------------------------------|------|-----|----------|----------|--------|
| GetRequestBench | benchGuzzle  | curl,guzzle                         | 1    | 100 | 1.708mb  | 3.073ms  | ±5.12% |
| GetRequestBench | benchGuzzle  | curl_multi,guzzle                   | 1    | 100 | 1.708mb  | 3.871ms  | ±5.51% |
| GetRequestBench | benchGuzzle  | stream,guzzle                       | 1    | 100 | 1.708mb  | 2.944ms  | ±5.76% |
| GetRequestBench | benchGuzzle  | curl,nyholm                         | 1    | 100 | 1.708mb  | 4.262ms  | ±5.45% |
| GetRequestBench | benchGuzzle  | curl_multi,nyholm                   | 1    | 100 | 1.708mb  | 5.110ms  | ±4.95% |
| GetRequestBench | benchGuzzle  | stream,nyholm                       | 1    | 100 | 1.708mb  | 4.159ms  | ±4.77% |
| GetRequestBench | benchGuzzle  | curl,diactoros                      | 1    | 100 | 1.708mb  | 3.491ms  | ±5.01% |
| GetRequestBench | benchGuzzle  | curl_multi,diactoros                | 1    | 100 | 1.708mb  | 4.317ms  | ±4.33% |
| GetRequestBench | benchGuzzle  | stream,diactoros                    | 1    | 100 | 1.708mb  | 3.406ms  | ±6.07% |
| GetRequestBench | benchBuzz    | curl,guzzle,guzzle                  | 1    | 100 | 1.708mb  | 2.554ms  | ±7.06% |
| GetRequestBench | benchBuzz    | multiCurl,guzzle,guzzle             | 1    | 100 | 1.708mb  | 2.487ms  | ±7.08% |
| GetRequestBench | benchBuzz    | fileGetContents,guzzle,guzzle       | 1    | 100 | 1.708mb  | 2.630ms  | ±5.44% |
| GetRequestBench | benchBuzz    | curl,nyholm,guzzle                  | 1    | 100 | 1.708mb  | 2.939ms  | ±5.94% |
| GetRequestBench | benchBuzz    | multiCurl,nyholm,guzzle             | 1    | 100 | 1.708mb  | 2.891ms  | ±4.96% |
| GetRequestBench | benchBuzz    | fileGetContents,nyholm,guzzle       | 1    | 100 | 1.708mb  | 3.642ms  | ±4.96% |
| GetRequestBench | benchBuzz    | curl,diactoros,guzzle               | 1    | 100 | 1.708mb  | 2.806ms  | ±7.44% |
| GetRequestBench | benchBuzz    | multiCurl,diactoros,guzzle          | 1    | 100 | 1.708mb  | 2.718ms  | ±3.98% |
| GetRequestBench | benchBuzz    | fileGetContents,diactoros,guzzle    | 1    | 100 | 1.708mb  | 2.894ms  | ±7.01% |
| GetRequestBench | benchBuzz    | curl,guzzle,nyholm                  | 1    | 100 | 1.708mb  | 2.455ms  | ±6.81% |
| GetRequestBench | benchBuzz    | multiCurl,guzzle,nyholm             | 1    | 100 | 1.708mb  | 2.399ms  | ±3.62% |
| GetRequestBench | benchBuzz    | fileGetContents,guzzle,nyholm       | 1    | 100 | 1.708mb  | 3.535ms  | ±5.69% |
| GetRequestBench | benchBuzz    | curl,nyholm,nyholm                  | 1    | 100 | 1.708mb  | 2.207ms  | ±6.89% |
| GetRequestBench | benchBuzz    | multiCurl,nyholm,nyholm             | 1    | 100 | 1.708mb  | 2.148ms  | ±4.99% |
| GetRequestBench | benchBuzz    | fileGetContents,nyholm,nyholm       | 1    | 100 | 1.708mb  | 2.247ms  | ±6.48% |
| GetRequestBench | benchBuzz    | curl,diactoros,nyholm               | 1    | 100 | 1.708mb  | 2.407ms  | ±6.83% |
| GetRequestBench | benchBuzz    | multiCurl,diactoros,nyholm          | 1    | 100 | 1.708mb  | 2.330ms  | ±4.23% |
| GetRequestBench | benchBuzz    | fileGetContents,diactoros,nyholm    | 1    | 100 | 1.708mb  | 2.507ms  | ±6.95% |
| GetRequestBench | benchBuzz    | curl,guzzle,diactoros               | 1    | 100 | 1.708mb  | 2.402ms  | ±6.40% |
| GetRequestBench | benchBuzz    | multiCurl,guzzle,diactoros          | 1    | 100 | 1.708mb  | 2.369ms  | ±5.27% |
| GetRequestBench | benchBuzz    | fileGetContents,guzzle,diactoros    | 1    | 100 | 1.708mb  | 3.500ms  | ±6.90% |
| GetRequestBench | benchBuzz    | curl,nyholm,diactoros               | 1    | 100 | 1.708mb  | 2.445ms  | ±6.13% |
| GetRequestBench | benchBuzz    | multiCurl,nyholm,diactoros          | 1    | 100 | 1.708mb  | 2.409ms  | ±3.77% |
| GetRequestBench | benchBuzz    | fileGetContents,nyholm,diactoros    | 1    | 100 | 1.708mb  | 3.182ms  | ±4.99% |
| GetRequestBench | benchBuzz    | curl,diactoros,diactoros            | 1    | 100 | 1.708mb  | 1.642ms  | ±7.95% |
| GetRequestBench | benchBuzz    | multiCurl,diactoros,diactoros       | 1    | 100 | 1.708mb  | 1.613ms  | ±4.84% |
| GetRequestBench | benchBuzz    | fileGetContents,diactoros,diactoros | 1    | 100 | 1.708mb  | 1.723ms  | ±8.52% |
| GetRequestBench | benchReact   | guzzle                              | 1    | 100 | 1.707mb  | 8.835ms  | ±4.28% |
| GetRequestBench | benchReact   | nyholm                              | 1    | 100 | 1.707mb  | 8.531ms  | ±4.78% |
| GetRequestBench | benchReact   | diactoros                           | 1    | 100 | 1.707mb  | 7.753ms  | ±4.17% |
| GetRequestBench | benchAmphp   |                                     | 1    | 100 | 1.707mb  | 13.393ms | ±2.56% |
| GetRequestBench | benchLaminas |                                     | 1    | 100 | 1.707mb  | 4.989ms  | ±5.09% |
| GetRequestBench | benchSymfony | native                              | 1    | 100 | 1.707mb  | 2.636ms  | ±4.83% |
| GetRequestBench | benchSymfony | curl                                | 1    | 100 | 1.707mb  | 2.986ms  | ±4.56% |
