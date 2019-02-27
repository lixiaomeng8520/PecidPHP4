<?php

require '../vendor/autoload.php';



$app = \PecidPHP4\App::getInstance(dirname(__FILE__) . '/../blog/config');
$users = $app->get('/users', '\Blog\Controller\User:getUsers', 'getUsers');
$user = $app->get('/user/{name}', '\Blog\Controller\User:getUser', 'getUser');
//$user->add(new \Blog\Middleware\Login());
$app->run();


