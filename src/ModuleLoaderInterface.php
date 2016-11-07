<?php
namespace Cadre\Module;

use Aura\Di\ContainerConfigInterface;

interface ModuleLoaderInterface extends ContainerConfigInterface
{
    public function isDev();
    public function loaded($name);
}
