<?php
namespace PecidPHP4;

use Monolog\Logger;
use Noodlehaus\ConfigInterface;
use Noodlehaus\Config;
use PecidPHP4\Exception\MethodNotAllowedException;
use PecidPHP4\Exception\NotFoundException;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Relay\Relay;
use Whoops\Handler\Handler;
use Whoops\Run;
use Zend\Diactoros\ServerRequestFactory;

use FastRoute\Dispatcher;
use FastRoute\RouteCollector;

class App extends Handler
{
    private $container;
    private $config;
    private $logger;

    private $routes = [];
    private $middlewares = [];

    /* @var ServerRequestInterface */
    private $request;

    /* @var Route */
    private $route;

    public static function getInstance(string $app_config_path)
    {
        return new static($app_config_path);
    }

    public function __construct(string $app_config_path)
    {
        $this->request = ServerRequestFactory::fromGlobals($_SERVER, $_GET, $_POST, $_COOKIE, $_FILES);
        $this->request = $this->request->withAttribute('app', $this);

        (new Run())->pushHandler($this)->register();

        $this->config = new Config(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'config.json');
        file_exists($app_config_path) && $this->config->merge(new Config($app_config_path));

        $routes = $this->config->get('routes');
        foreach ($routes as $route) {
            $this->map($route['name'], $route['method'], $route['pattern'], $route['handler']);
        }
    }

    /****************************Component*************************************/

    /// Container

    public function getContainer() : ContainerInterface
    {
        if (!$this->container) {
            $this->container = new Container();
        }
        return $this->container;
    }

    /// Config

    public function getConfig() : ConfigInterface
    {
        return $this->config;
    }

    /// Logger

    public function getLogger() : LoggerInterface
    {
        if (!$this->logger) {
            $this->logger = new Logger('PecidPHP4');
        }
        return $this->logger;
    }

    /****************************Route*****************************************/

    public function get(string $pattern, $handler, string $name) : Route
    {
        return $this->map('GET', $pattern, $handler, $name);
    }

    public function post(string $pattern, $handler, string $name) : Route
    {
        return $this->map('POST', $pattern, $handler, $name);
    }

    public function map(string $name, string $method, string $pattern, $handler) : Route
    {
        $route = new Route($method, $pattern, $handler, $name);
        $this->routes[$name] = $route;
        return $route;
    }

    public function getRoute() : Route
    {
        return $this->route;
    }

    /****************************Middleware*************************************/

    public function add($middleware)
    {
        array_push($this->middlewares, $middleware);
    }

    /****************************Run********************************************/

    public function run()
    {
        $dispatcher = \FastRoute\simpleDispatcher(function (RouteCollector $r) {
            foreach ($this->routes as $id => $route) {
                $r->addRoute($route->method, $route->pattern, $id);
            }
        });

        $routeInfo = $dispatcher->dispatch($this->request->getMethod(), $this->request->getUri()->getPath());

        switch ($routeInfo[0]) {
            case Dispatcher::NOT_FOUND:
                throw new NotFoundException('404 Not Found');
            case Dispatcher::METHOD_NOT_ALLOWED:
                throw new MethodNotAllowedException('405 Method Not Allowed');
            default:
                $this->route = $this->routes[$routeInfo[1]];
                $this->route->args = $routeInfo[2];
                $this->add($this->route);
                $response = (new Relay($this->middlewares))->handle($this->request);
                $this->respond($response);
        }
    }

    public function handle()
    {
        $exception = $this->getException();
        if ($exception instanceof NotFoundException) {
            $handler = $this->config->get('notFoundHandler');

        } elseif ($exception instanceof MethodNotAllowedException) {
            $handler = $this->config->get('methodNotAllowedHandler');
        } else {
            $handler = $this->config->get('errorHandler');
        }
        $response = call_user_func_array([new $handler(), 'handle'], [$this->request, $exception]);
        $this->respond($response);
    }

    private function respond(ResponseInterface $response)
    {
        header(sprintf(
            'HTTP/%s %s %s',
            $response->getProtocolVersion(),
            $response->getStatusCode(),
            $response->getReasonPhrase()
        ));
        foreach ($response->getHeaders() as $k => $v_a) {
            $replace = $k === 'Set-Cookie' ? false : true;
            foreach ($v_a as $v) {
                header("${k}: ${v}", $replace);
            }
        }
        echo $response->getBody();
    }
}
