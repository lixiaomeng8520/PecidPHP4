<?php
namespace PecidPHP4\ExceptionHandler;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface ExceptionHandlerInterface
{
    public function handle(ServerRequestInterface $request, \Throwable $throwable) : ResponseInterface;
}