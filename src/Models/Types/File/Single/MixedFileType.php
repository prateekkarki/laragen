<?php
namespace Prateekkarki\Laragen\Models\Types\File\Single;
use Prateekkarki\Laragen\Models\Types\File\SingleType;

class MixedFileType extends SingleType
{
    protected $hasFile = true;
    protected $extensions = '.zip,.doc,.txt,.pdf,.csv';
    protected $formType = 'file';
}
