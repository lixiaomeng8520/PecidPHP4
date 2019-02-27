<?php
namespace PecidPHP4\ExceptionHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class Handler implements ExceptionHandlerInterface
{
    protected $code = 500;

    public function handle(ServerRequestInterface $request, \Throwable $throwable): ResponseInterface
    {
        $response = new Response();
        $response = $response->withStatus($this->code);
        $message = $throwable->getFile() . ' : ' . $throwable->getLine() . ' : ' . $throwable->getMessage();
        $response->getBody()->write($message);
        return $response;
    }
}