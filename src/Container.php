<?php
namespace PecidPHP4;

use Psr\Container\ContainerInterface;
use Pimple\Container as PimpleContainer;

class Container extends PimpleContainer implements ContainerInterface
{

    public function get($id)
    {
        return $this[$id];
    }

    public function has($id)
    {
        return isset($this[$id]);
    }

}