<?php
namespace Cadre\Module\Sample;

class Value
{
    public $value;

    public function __construct($value = 'undefined')
    {
        $this->value = $value;
    }
}
