<?php
namespace Cadre\Module;

use Aura\Di\Container;

class Module implements ModuleInterface
{
    private $loader;

    public function __construct(ModuleLoaderInterface $loader)
    {
        $this->loader = $loader;
    }

    public function loader()
    {
        return $this->loader;
    }

    public function require()
    {
        return [];
    }

    public function conflict()
    {
        return [];
    }

    public function replace()
    {
        return [];
    }

    public function define(Container $di)
    {
    }

    public function modify(Container $di)
    {
    }
}
