<?php
namespace Cadre\Module;

use RuntimeException;

class ConflictingModuleException extends RuntimeException
{
    public function __construct($module, $with)
    {
        parent::__construct(sprintf('%s conflicts with %s', $module, $with));
    }
}
