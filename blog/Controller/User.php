<?php
namespace Blog\Controller;

use PecidPHP4\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response;

class User
{

    public function getUsers(ServerRequestInterface $request, RequestHandlerInterface $handler)
    {
        /* @var App $app */
        $app = $request->getAttribute('app');

        $response = $handler->handle($request);

        $view = $app->getView();
        $response = $view->render($response, 'users.php');

        return $response;
    }

    public function getUser(ServerRequestInterface $request)
    {



        $response = new Response();
        $response->getBody()->write('dddd');
        return $response;
    }
}