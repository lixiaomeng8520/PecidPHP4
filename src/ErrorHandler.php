<?php
namespace PecidPHP4;

use Whoops\Handler\PlainTextHandler;

class ErrorHandler extends PlainTextHandler
{
    public function handle()
    {
        echo 'MyHandler : ' . $this->getException()->getMessage();
    }
}