<?php
namespace PecidPHP4;

use Psr\Http\Message\ServerRequestInterface;
use Relay\Relay;
use Zend\Diactoros\Response;

class Route
{
    private $method;
    private $pattern;
    private $handler;
    private $name;
    private $args = [];

    private $middlewares = [];

    public function __construct($name, $method, $pattern, $handler, $args = [], $middlewares = [])
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->name = $name;
        $this->args = $args;
        $this->middlewares = $middlewares;
    }

    public function __get($name)
    {
        return $this->$name;
    }

    public function __set($name, $val)
    {
        $this->$name = $val;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if (is_string($this->handler)) {
            $c_a = explode(':', $this->handler);
            $controller = $c_a[0];
            $action = $c_a[1];
            if (!class_exists($controller)) {
                throw new \Exception("controller ${controller} not found");
            }
            if (!method_exists($controller, $action)) {
                throw new \Exception("action ${controller}:${action}  not found");
            }
            $handler = [new $controller(), $action];
        } elseif (is_callable($this->handler)) {
            $handler = $this->handler;
        } else {
            throw new \Exception('handler can not be resolved');
        }
        $this->add($handler);
        $this->add(function () {
            return new Response();
        });
        return (new Relay($this->middlewares))->handle($request);
    }

    public function add($middleware)
    {
        array_push($this->middlewares, $middleware);
    }

}