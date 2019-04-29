<?php
namespace Prateekkarki\Laragen\Models\Types\Relational\Multiple;
use Illuminate\Support\Str;

use Prateekkarki\Laragen\Models\Types\Relational\MultipleType;

class RelatedType extends MultipleType
{
    public function getParentModule() {
        return $this->typeOption;
    }

	public function getParentModel() {
        return ucfirst(camel_case(Str::singular($this->typeOption)));
    }
}
