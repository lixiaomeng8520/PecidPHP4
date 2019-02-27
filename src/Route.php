<?php
namespace PecidPHP4;

use Psr\Http\Message\ServerRequestInterface;
use Relay\Relay;

class Route
{

    /**
     * @var string
     */
    private $method;

    /**
     * @var string
     */
    private $pattern;

    /**
     * @var
     */
    private $handler;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    public static $id = 0;

    private $middlewares = [];

    public $args = [];

    public function __construct(string $method, string $pattern, $handler, string $name = '')
    {
        $this->method = $method;
        $this->pattern = $pattern;
        $this->handler = $handler;
        $this->name = $name;
    }

    public function __get($name)
    {
        return $this->$name;
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
        } elseif ($this->handler instanceof \Closure) {
            $handler = $this->handler;
        } else {
            throw new \Exception('handler can not be resolved');
        }
        $this->add($handler);
        return (new Relay($this->middlewares))->handle($request);
    }

    public function add($middleware)
    {
        array_push($this->middlewares, $middleware);
    }

}