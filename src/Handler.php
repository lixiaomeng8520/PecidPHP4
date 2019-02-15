<?php
namespace PecidPHP4;

use Psr\Http\Message\RequestInterface;

class Handler {

    private $handler;
    private $args;

    public function __construct($handler, $args)
    {
        $this->handler = $handler;
        $this->args = $args;
    }

    public function __invoke(RequestInterface $request)
    {

    }

}