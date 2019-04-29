<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;

use Prateekkarki\Laragen\Models\Types\RelationalType;
use Illuminate\Support\Str;

class RelatedType extends RelationalType
{
    public function getPivotSchema($modelName, $moduleName)
    {
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');".PHP_EOL.$this->getTabs(3);

        $schema .= '$table->bigInteger("'. Str::singular($this->columnName) .'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('". Str::singular($this->columnName) ."_id')->references('id')->on('".$this->columnName."')->onDelete('set null');".PHP_EOL;
        return $schema;
    }
    
    public function getParentModule() {
        return $this->typeOption;
    }

    public function getParentModel() {
        return ucfirst(camel_case(Str::singular($this->typeOption)));
    }
}