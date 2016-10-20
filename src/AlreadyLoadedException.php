<?php
namespace Cadre\Module;

use RuntimeException;

class AlreadyLoadedException extends RuntimeException
{
    public function __construct($module, $verb, $replacesModule)
    {
        parent::__construct(sprintf(
            '%s cannot %s %s because it is already loaded',
            $module,
            $verb,
            $replacesModule
        ));
    }
}
