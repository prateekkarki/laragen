<?php
namespace Prateekkarki\Laragen\Models\Types\File\Multiple;
use Prateekkarki\Laragen\Models\Types\File\MultipleType;

class GalleryType extends MultipleType
{
    protected $hasImage = true;
	protected $formType = 'gallery';

    public function getLaragenColumns()
    {
        return array_merge([
            'filename' => 'image', 
            'size' => 'integer'
        ], [$this->getParentModelLowercase() => 'parent:'.$this->getParentModule()]);
    }
}
