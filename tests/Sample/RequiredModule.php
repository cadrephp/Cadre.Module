<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class RequiredModule extends Module
{
    public function define(Container $di)
    {
        $di->params[Value::class]['value'] = 'required';
    }
}
