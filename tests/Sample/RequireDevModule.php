<?php
namespace Cadre\Module\Sample;

use Aura\Di\Container;
use Cadre\Module\Module;
use Exception;

class RequireDevModule extends Module
{
    public function requireDev()
    {
        return [
            RequiredModule::class,
        ];
    }
}
