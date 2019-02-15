<?php
namespace Blog\Controller;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class User {

    public function getUsers(RequestInterface $request) {
        $response = new Response();
        $response->getBody()->write('users:');
        return $response;
    }

    public function getUser(ServerRequestInterface $request) {
        $args = $request->getAttribute('args');
        $response = new Response();
        $response->getBody()->write($args['name'] . ':');
        return $response;
    }
}