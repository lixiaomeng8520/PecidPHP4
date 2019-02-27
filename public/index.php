<?php
//define('ROOT_PATH', dirname(__FILE__));

//class A
//{
//    private $a = 1;
//
//    public function getA()
//    {
//        return $this->a;
//    }
//}
//
//class B extends A
//{
//
//}
//
//$b = new B();
//echo $b->getA();
//
//
//
//
//
//die;


require '../vendor/autoload.php';


$app = \PecidPHP4\App::getInstance(dirname(__FILE__) . '/../blog/config');
$users = $app->get('/users', '\Blog\Controller\User:getUsers');


$user = $app->get('/user/{name}', '\Blog\Controller\User:getUser');



$user->add(new \Blog\Middleware\Login());

$app->run();


