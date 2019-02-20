<?php
namespace Blog\Controller;

use PecidPHP4\App;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class User
{

    public function getUsers(ServerRequestInterface $request)
    {
        $response = new Response();
        $response->getBody()->write('users:');
        $a = $b + 1;
        return $response;
    }

    public function getUser(ServerRequestInterface $request)
    {
        $args = $request->getAttribute('args');
        $response = new Response();
        $response->getBody()->write($args['name'] . ':');
        return $response;
    }
}