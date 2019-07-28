<?php
namespace Prateekkarki\Laragen\Models\Types\File\Multiple;
use Prateekkarki\Laragen\Models\Types\File\MultipleType;

class AttachmentType extends MultipleType
{
    protected $hasFile = true;
    protected $formType = 'multipleFiles';

    public function getLaragenColumns()
    {
        return array_merge([
            'filename' => 'file', 
            'size' => 'integer'
        ], [$this->getParentModelLowercase() => 'parent:'.$this->getParentModule()]);
    }
}
