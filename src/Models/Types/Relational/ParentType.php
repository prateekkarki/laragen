<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;

use Prateekkarki\Laragen\Models\Types\RelationalType;
use Illuminate\Support\Str;

class ParentType extends RelationalType
{
    protected $dataType = 'integer';
    
    public function getSchema()
    {
        $schema = "";
        $parent = $this->typeOption;
        $schema .= "\$table->bigInteger('".str_singular($parent)."_id')->unsigned()->nullable();".PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".str_singular($parent)."_id')->references('id')->on('$parent')->onDelete('set null');".PHP_EOL;
        return $schema;
    }
    
    public function getParentModule() {
        return $this->typeOption;
    }

    public function getParentModel() {
        return ucfirst(camel_case(Str::singular($this->typeOption)));
    }
}
