<?php namespace App\Controllers;

use Interop\Container\ContainerInterface;

abstract class Controller {
    protected $ci;
    
    public function __construct(ContainerInterface $container)
    {
        $this->ci = $container;
    }
}use Foo\Bar\Baz;
