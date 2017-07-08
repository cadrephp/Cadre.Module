<?php
namespace Cadre\Module;

use RuntimeException;

class CircularReferenceException extends RuntimeException
{
    public function __construct($module, $count)
    {
        parent::__construct(sprintf('%s is part of a circular reference: %d', $module, $count));
    }
}
