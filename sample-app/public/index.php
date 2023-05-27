<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require_once __DIR__ . '/../vendor/autoload.php';

$app = Slim\Factory\AppFactory::create();

$app->get('/', static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $body = '<!DOCTYPE html>Hello world.';
    $response->getBody()->write($body);
    return $response->withHeader('Content-Length', strlen($body));
});

$app->get('/json', static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $body = '{"hello":"world."}';
    $response->getBody()->write($body);
    return $response
        ->withHeader('Content-Length', strlen($body))
        ->withHeader('Content-Type', 'application/json; charset=UTF-8');
});

$app->run();
