<?php
namespace Blog\Controller;

use PecidPHP4\App;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response;

class User
{

    public function getUsers(ServerRequestInterface $request)
    {
        /* @var App $app */
        $app = $request->getAttribute('app');
        $config = $app->getConfig();
        var_dump($config->get('route'));die;
        return $response;
    }

    public function getUser(ServerRequestInterface $request)
    {



        $response = new Response();
        $response->getBody()->write('dddd');
        return $response;
    }
}