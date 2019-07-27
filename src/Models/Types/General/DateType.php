<?php
namespace Prateekkarki\Laragen\Models\Types\General;
use Prateekkarki\Laragen\Models\Types\GeneralType;

class DateType extends GeneralType
{
	protected $dataType = 'date';
	protected $formType = 'date';
	protected $size = false;
	protected $validationRule = 'date_format:Y-m-d';

}
