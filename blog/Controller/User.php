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
        /* @var App $app */
        $app = $request->getAttribute('app');
        $logger = $app->getLogger();
        $logger->warning('nihaonihaonihao');

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