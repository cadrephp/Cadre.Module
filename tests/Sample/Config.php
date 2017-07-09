<?php
namespace Cadre\Module\Sample;

use Aura\Di\Container;
use Aura\Di\ContainerConfig;

class Config extends ContainerConfig
{
    public function define(Container $di)
    {
        $di->params[Value::class]['value'] = 'required';
    }
}
