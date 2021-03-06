<?php
namespace Prateekkarki\Laragen\Models\Types;

class FileType extends LaragenType
{
    protected $dataType = 'string';
    protected $formType = 'file';
    protected $extensions = '.png,.jpg,.gif,.bmp,.jpeg';
    protected $isRelational = false;
}
