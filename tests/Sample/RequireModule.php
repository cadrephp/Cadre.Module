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

    public function define(Container $di)
    {
        if (empty($di->params[Value::class]['value'])) {
            throw new Exception('Expected RequiredModule Already');
        }
    }
}
