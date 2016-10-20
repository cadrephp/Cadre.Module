<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class RequireDevModule extends Module
{
    public function requireDev()
    {
        return [
            RequiredModule::class,
        ];
    }
}
