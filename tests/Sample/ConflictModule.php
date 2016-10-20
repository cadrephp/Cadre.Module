<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class ConflictModule extends Module
{
    public function conflict()
    {
        return [
            RequiredModule::class,
        ];
    }
}
