<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class ReplaceModule extends Module
{
    public function replace()
    {
        return [
            RequiredModule::class,
        ];
    }

    public function define(Container $di)
    {
        $di->params[Value::class]['value'] = 'replace';
    }
}
