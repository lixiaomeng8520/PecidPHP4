<?php
namespace PecidPHP4\ExceptionHandler;

class MethodNotAllowedHandler
{
    public function __invoke(\Throwable $t)
    {
        echo self::class . ' : ' . $t->getMessage();
    }
}