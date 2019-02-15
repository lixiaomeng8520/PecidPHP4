<?php
//define('ROOT_PATH', dirname(__FILE__));


require '../vendor/autoload.php';


$app = \PecidPHP4\App::run();

//$app->get('/users', '\Blog\Controller\User:getUsers');
//$app->get('/users', function(\Psr\Http\Message\RequestInterface $request) {
//    var_dump($this);
//    $response = new \Zend\Diactoros\Response();
//    $response->getBody()->write('function users');
//    return $response;
//});


$users = $app->get('/users', '\Blog\Controller\User:getUsers');


//$user = $app->get('/user/{name}', '\Blog\Controller\User:getUser');

$user = $app->get('/user/{name}', function(\Psr\Http\Message\ServerRequestInterface $request) {
    $args = $request->getAttribute('args');
    $response = new Zend\Diactoros\Response();
    $response->getBody()->write($args['name'] . ':');
    return $response;
});

$user->add(new \Blog\Middleware\Login());

$app->dispatch();


