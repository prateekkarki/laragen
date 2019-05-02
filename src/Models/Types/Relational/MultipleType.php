<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;
use Prateekkarki\Laragen\Models\Types\RelationalType;
use Illuminate\Support\Str;

class MultipleType extends RelationalType
{
    public function getPivotSchema()
    {
        $modelName = $this->getParentModelLowercase();
        $moduleName = $this->getParentModule();
        $schema = PHP_EOL.$this->getTabs(3);
        $schema .= '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');".PHP_EOL.$this->getTabs(3);

        $schema .= '$table->bigInteger("'. Str::singular($this->columnName) .'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('". Str::singular($this->columnName) ."_id')->references('id')->on('".$this->columnName."')->onDelete('set null');".PHP_EOL;
        return $schema;
    }
}
