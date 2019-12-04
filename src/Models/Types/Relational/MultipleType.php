<?php

namespace Prateekkarki\Laragen\Models\Types\Relational;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\Types\RelationalType;

class MultipleType extends RelationalType
{
    public function getResourceTransformer()
    {
        return $this->getRelatedModel() . 'Resource::collection($this->' . $this->getColumn() . '()->paginate(10))';
    }
}
