<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class CircularModuleA extends Module
{
    public function require()
    {
        return [
            CircularModuleB::class,
        ];
    }
}
