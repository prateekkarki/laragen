<?php

namespace Prateekkarki\Laragen\Models\Types\File\Single;

use Prateekkarki\Laragen\Models\Types\File\SingleType;

class ImageType extends SingleType
{
    protected $hasImage = true;
    protected $formType = 'image';
    protected $extensions = '.png,.jpg,.gif,.bmp,.jpeg';

    public function getResourceTransformer()
    {
        return '[
                \'xs\' => asset("images/' . $this->getParentModule() . '/xs/" . $this->' . $this->getColumnKey() . '),
                \'md\' => asset("images/' . $this->getParentModule() . '/md/" . $this->' . $this->getColumnKey() . '),
                \'sm\' => asset("images/' . $this->getParentModule() . '/sm/" . $this->' . $this->getColumnKey() . ')
            ]';
    }
}
