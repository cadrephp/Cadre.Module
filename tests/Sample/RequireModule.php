<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class RequireModule extends Module
{
    public function require()
    {
        return [
            RequiredModule::class,
        ];
    }
}
