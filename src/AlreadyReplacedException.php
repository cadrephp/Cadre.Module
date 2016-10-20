<?php
namespace Cadre\Module;

use RuntimeException;

class AlreadyReplacedException extends RuntimeException
{
    public function __construct($module, $replacesModule, $alreadyReplacedWith)
    {
        parent::__construct(sprintf(
            '%s cannot replace %s because it is already replaced with %s',
            $module,
            $replacesModule,
            $alreadyReplacedWith
        ));
    }
}
