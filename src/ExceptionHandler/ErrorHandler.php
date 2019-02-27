<?php
namespace PecidPHP4\ExceptionHandler;

class ErrorHandler
{
    public function __invoke(\Throwable $t)
    {
        echo self::class . ' : ' . $t->getMessage();
    }
}