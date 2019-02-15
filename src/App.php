<?php
namespace PecidPHP4;

use Pimple\Container;

use Noodlehaus\ConfigInterface;
use Noodlehaus\Config;

use Relay\Relay;
use Zend\Diactoros\ServerRequestFactory;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class App {

    private $middlewares = [];

    private $routes = [];

    /**
     * @var Container
     */
    private $container;

    public function getContainer() : Container
    {
        return $this->container;
    }

    public static function getInstance() {
        return new static();
    }

    public function __construct()
    {
        $this->initContainer();
        $this->initConfig();
    }

    public function get(string $pattern, $handler, string $name = '') : Route {
        return $this->map('GET', $pattern, $handler, $name);
    }

    public function post(string $pattern, $handler, string $name = '') : Route {
        return $this->map('POST', $pattern, $handler, $name);
    }

    public function map(string $method, string $pattern, $handler, string $name) : Route {
    //  preg_match('/^([a-zA-Z0-9_\\]+?):([a-zA-Z0-9_]+)$/', $pattern, $matches);
        $route = new Route($method, $pattern, $handler, $name);
        $this->routes[Route::$id] = $route;
        Route::$id++;
        return $route;
    }

    public function add($middleware) {
        array_push($this->middlewares, $middleware);
    }

    public function run() {
        try {
            $this->dispatch();
        } catch (\Exception $e) {
            echo 'exception-----------------' . $e->getMessage() . $e->getFile() . $e->getLine();
        } catch (\Error $e) {
            echo 'error---------------------' . $e->getMessage() . $e->getFile() . $e->getLine();
        } catch (\Throwable $t) {
            echo 'throwable-----------------' . $t->getMessage();
        }
    }


    private function dispatch() {
        $dispatcher = \FastRoute\simpleDispatcher(function(RouteCollector $r) {
            foreach($this->routes as $id => $route) {
                $r->addRoute($route->method, $route->pattern, $id);
            }
        });

        $request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $routeInfo = $dispatcher->dispatch($request->getMethod(), $request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new \Exception('404 not found');
            default:
                $route = $this->routes[$routeInfo[1]];
                $route->args = $routeInfo[2];
                $request = $request->withAttribute('route', $route);
                $request = $request->withAttribute('container', $this->container);
                $this->add($route);
                $response = (new Relay($this->middlewares))->handle($request);
                echo $response->getBody();
        }
    }

    private function initContainer() {
        $this->container = new Container();
    }

    private function initConfig() {
        $this->container['config'] = function() : ConfigInterface {
            $config = new Config(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.json');
            $app_config_path = dirname(getcwd()) . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR . 'config';
            file_exists($app_config_path) && $config->merge(new Config($app_config_path));
            return $config;
        };
    }
}