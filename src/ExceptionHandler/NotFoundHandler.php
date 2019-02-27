<?php
namespace PecidPHP4\ExceptionHandler;

class NotFoundHandler
{
    public function __invoke(\Throwable $t)
    {
        echo self::class . ' : ' . $t->getMessage();
    }
}