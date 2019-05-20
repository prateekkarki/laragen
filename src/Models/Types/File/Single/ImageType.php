<?php
namespace Prateekkarki\Laragen\Models\Types\File\Single;
use Prateekkarki\Laragen\Models\Types\File\SingleType;

class ImageType extends SingleType
{
	protected $hasImage = true;
	protected $formType = 'image';
	protected $extensions = '.png,.jpg,.gif,.bmp,.jpeg';
}
