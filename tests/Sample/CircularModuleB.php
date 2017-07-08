<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class CircularModuleB extends Module
{
    public function require()
    {
        return [
            CircularModuleA::class,
        ];
    }
}
