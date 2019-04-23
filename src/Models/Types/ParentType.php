<?php
namespace Prateekkarki\Laragen\Models\Types;
use Prateekkarki\Laragen\Models\DataOption;
use Illuminate\Support\Str;

class ParentType extends LaragenType
{
    public function getSchema()
    {
        $schema = "";
        $parent = $this->typeOption;
        $schema .= "\$table->bigInteger('".str_singular($parent)."_id')->unsigned()->nullable();".PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".str_singular($parent)."_id')->references('id')->on('$parent')->onDelete('set null');".PHP_EOL;
        return $schema;
    }
    
    public function getParentModule() {
        return (in_array($this->dataType, DataOption::$specialTypes)) ? $this->typeOption : '';
    }

    public function getParentModel() {
        return (in_array($this->dataType, DataOption::$specialTypes)) ? ucfirst(camel_case(Str::singular($this->typeOption))) : '';
    }

}
