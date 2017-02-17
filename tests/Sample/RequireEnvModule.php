<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class RequireEnvModule extends Module
{
    public function requireSpecialEnvironment()
    {
        return [
            RequiredModule::class,
        ];
    }
}
