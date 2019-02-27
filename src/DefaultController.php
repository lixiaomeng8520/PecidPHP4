<?php
namespace PecidPHP4;

use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class DefaultController
{
    public function index(ServerRequestInterface $request)
    {
        $response = new Response();
        $response->getBody()->write('<h2>Welcome to PecidPHP4</h2>');
        return $response;
    }
}