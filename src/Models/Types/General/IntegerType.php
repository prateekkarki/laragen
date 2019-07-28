<?php
namespace Prateekkarki\Laragen\Models\Types\General;
use Prateekkarki\Laragen\Models\Types\GeneralType;

class IntegerType extends GeneralType
{
    protected $dataType = 'integer';
    protected $formType = 'integer';
    protected $size = false;
}
