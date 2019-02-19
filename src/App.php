<?php
namespace PecidPHP4;

use Monolog\Logger;
use Noodlehaus\ConfigInterface;
use Noodlehaus\Config;

use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Relay\Relay;
use Zend\Diactoros\ServerRequestFactory;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class App
{

    private $middlewares = [];

    private $routes = [];

    /**
     * @var Container
     */
    private $container;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function getContainer() : ContainerInterface
    {
        return $this->container;
    }

    public function getLogger() : LoggerInterface
    {
        return $this->logger;
    }

    public static function getInstance()
    {
        return new static();
    }

    public function __construct()
    {
        set_error_handler([$this, 'errorHandler']);
        set_exception_handler([$this, 'exceptionHandler']);
        register_shutdown_function([$this, 'shutdownHandler']);

        $this->initContainer();
        $this->initConfig();
        $this->initLogger();
    }

    public function get(string $pattern, $handler, string $name = '') : Route
    {
        return $this->map('GET', $pattern, $handler, $name);
    }

    public function post(string $pattern, $handler, string $name = '') : Route
    {
        return $this->map('POST', $pattern, $handler, $name);
    }

    public function map(string $method, string $pattern, $handler, string $name) : Route
    {
    //  preg_match('/^([a-zA-Z0-9_\\]+?):([a-zA-Z0-9_]+)$/', $pattern, $matches);
        $route = new Route($method, $pattern, $handler, $name);
        $this->routes[Route::$id] = $route;
        Route::$id++;
        return $route;
    }

    public function add($middleware)
    {
        array_push($this->middlewares, $middleware);
    }

    public function run()
    {
        $this->dispatch();
    }

    public function errorHandler(int $errno, string $errstr)
    {
        $this->logger->error($errstr);
    }

    public function exceptionHandler(\Throwable $t)
    {
        $this->logger->error($t->getMessage());
    }

    public function shutdownHandler()
    {
        if ($error = error_get_last()) {
            $this->logger->error($error);
        }
    }

    private function dispatch()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $id => $route) {
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
                $request = $request->withAttribute('app', $this);
                $this->add($route);
                $response = (new Relay($this->middlewares))->handle($request);
                echo $response->getBody();
        }
    }

    private function initContainer()
    {
        $this->container = new Container();
    }

    private function initConfig()
    {
        $this->container['config'] = function () : ConfigInterface {
            $config = new Config(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.json');
            $app_config_path = dirname(getcwd()) . DIRECTORY_SEPARATOR . 'blog' . DIRECTORY_SEPARATOR . 'config';
            file_exists($app_config_path) && $config->merge(new Config($app_config_path));
            return $config;
        };
    }

    private function initLogger()
    {
        $this->logger = new Logger('PecidPHP4');
    }
}
