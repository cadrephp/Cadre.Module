<?php
namespace Cadre\Module;

use Aura\Di\ContainerConfigInterface;

interface ModuleInterface extends ContainerConfigInterface
{
    public function require();
    public function requireDev();
    public function conflict();
    public function replace();
}
