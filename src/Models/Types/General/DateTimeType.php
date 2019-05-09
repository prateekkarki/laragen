<?php
namespace Prateekkarki\Laragen\Models\Types\General;
use Prateekkarki\Laragen\Models\Types\GeneralType;

class DateTimeType extends GeneralType
{
	protected $dataType = 'datetime';
	protected $formType = 'datetime';
	protected $size = false;
}
