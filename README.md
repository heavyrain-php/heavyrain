# Heavyrain PHP - Loadtest tool

__Heavyrain is loadtest/stresstest tool made with PHP.__

You can test any HTTP services using scenario written in PHP.

## Installation

### Prerequisites

- PHP 8.1.0+ (web and runner instance)
- Any HTTP(S) service
    - Main target is JSON API server.
    - NOTE: Currently HTTP/3 is not supported due to lack of implementation of curl.

You may add .gitignore lines below.

```.gitignore
/heavyrain.phar
/_heavyrain.stub.php
```

### Download phar manually(**recommended**)

```sh
$ curl -fLO https://github.com/heavyrain-php/heavyrain/releases/download/v0.0.1/heavyrain.phar
$ php heavyrain.phar help
# Generate stub file for static analysis and IDE.
$ php heavyrain.phar generate:stub
```

### Download phar using phive

```sh
$ phive install https://github.com/heavyrain-php/heavyrain.git
$ ./tools/heavyrain help
# Generate stub file for static analysis and IDE.
$ ./tools/heavyrain generate:stub
```

### Require using composer(not recommended because of some dependencies)

```sh
$ composer require --dev heavyrain/heavyrain
$ ./vendor/bin/heavyrain help
# You may not do generate stub file because actual files exist in vendor.
```

## Scenario creation

```sh
$ mkdir scenarios
$ touch scenarios/sample_scenario.php
```

```php:scenarios/sample_scenario.php
<?php
// scenarios/sample_scenario.php

declare(strict_types=1);

use Heavyrain\Contracts\ClientInterface;

// You should use `static function` for better performance.
return static function (ClientInterface $cl): void {
    $cl->get('/')->assertBodyContains('hello');
};

```

## Execution

### 1. Using Web GUI

TODO

### 2. Using CLI

```sh
$ php heavyrain.phar run scenarios/sample_scenario.php http://localhost:8080/
```

## Report

TODO

## Contributing

Anytime you can contribute to this repository.

```sh
$ git clone https://github.com/heavyrain-php/heavyrain.git
$ cd heavyrain
$ composer install
$ composer ci
```

## Licenses

See [LICENSE](./LICENSE).

## Contacts

[Twitter @akai_inu](https://twitter.com/akai_inu)
