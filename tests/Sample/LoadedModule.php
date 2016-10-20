<?php
namespace Cadre\Module\Sample;

use Cadre\Module\Module;
use Aura\Di\Container;

class LoadedModule extends Module
{
    public function define(Container $di)
    {
        if ($this->loader()->loaded(ConflictModule::class)) {
            $di->params[Value::class]['value'] = 'loaded';
        } else {
            $di->params[Value::class]['value'] = 'not-loaded';
        }
    }
}
