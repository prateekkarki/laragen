<?php
namespace Prateekkarki\Laragen\Models\Types;

class FileType extends LaragenType
{
	protected $dataType = 'string';
	protected $hasFile = 'true';
	protected $extensions = '.png,.jpg,.gif,.bmp,.jpeg';

}
