<?php
namespace Cadre\Module;

use Aura\Di\ContainerConfigInterface;

interface ModuleLoaderInterface extends ContainerConfigInterface
{
    public function loaded($name);
}
