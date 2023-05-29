<?php

/**
 * @license MIT
 */

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Exception\HttpNotFoundException;
use Slim\Factory\AppFactory;
use Slim\Psr7\Response;
use Slim\Psr7\Stream;

require_once __DIR__ . '/../vendor/autoload.php';



// helper functions
function withJson(ResponseInterface $response, array $body): ResponseInterface
{
    $bodyStr = \json_encode($body);
    $stream = new Stream(\fopen('php://memory', 'rw+'));
    $stream->write($bodyStr);
    return $response
        ->withBody($stream)
        ->withHeader('Content-Length', \strlen($bodyStr))
        ->withHeader('Content-Type', 'application/json; charset=UTF-8');
}



// Middlewares
$acceptsJson = static function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $response = $handler->handle($request);
    $accept = $request->getHeaderLine('Accept');
    if ($accept !== '*/*' && !\str_starts_with($accept, 'application/json')) {
        return withJson($response, ['error' => \sprintf('Invalid Accept header provided: %s', $accept)])
            ->withStatus(400);
    }
    return $response;
};



// App
$app = AppFactory::create();



// App middlewares
$app->add(static function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    try {
        $response = $handler->handle($request);
    } catch (HttpNotFoundException $e) {
        return new Response(404);
    }

    return $response;
});



// Routes
$app->get('/', static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $body = '<!DOCTYPE html>Hello world.';
    $response->getBody()->write($body);
    return $response->withHeader('Content-Length', strlen($body));
});

$app->post('/json', static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $payload = $request->getBody()->__toString();
    return withJson($response, ['hello' => 'world.', 'payload' => $payload]);
})->add($acceptsJson);

$app->get('/users/{userId:[0-9]+}', static function (ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface {
    /** @var int $userId */
    $userId = \intval($args['userId']);

    if ($userId > 100) {
        return withJson($response, ['error' => 'User ' . $userId . ' not found'])
            ->withStatus(404);
    }

    return withJson($response, ['userId' => $userId, 'name' => 'DUMMY']);
})->add($acceptsJson);

$app->get('/posts/', static function (ServerRequestInterface $request, ResponseInterface $response): ResponseInterface {
    $queryString = $request->getUri()->getQuery();
    $qs = \explode('&', $queryString);
    $params = [];
    foreach ($qs as $str) {
        [$key, $value] = \explode('=', $str, 2);
        $params[$key] = $value;
    }
    if (!\array_key_exists('postId', $params)) {
        return withJson($response, ['error' => 'Post not found'])
            ->withStatus(404);
    }
    $postId = $params['postId'];
    return withJson($response, ['postId' => $postId, 'content' => 'DUMMY CONTENT']);
})->add($acceptsJson);

$app->run();
