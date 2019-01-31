<?php
require 'vendor/autoload.php';

$stream = new \Zend\Diactoros\Stream('1.txt', 'rw');

var_dump($stream->getMetadata());
