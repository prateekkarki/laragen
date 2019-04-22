<?php
namespace Prateekkarki\Laragen\Models\Types;
use Illuminate\Support\Str;

class RelatedType extends LaragenType
{
    public $isRelational = true;

    public function getTableSchema($modelName, $moduleName)
    {
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');";

        $schema .= '$table->bigInteger("'. Str::singular($this->columnName) .'_id")->unsigned()->nullable();';
        $schema .= "\$table->foreign('". Str::singular($this->columnName) ."_id')->references('id')->on('".$this->columnName."')->onDelete('set null');";
        return $schema;
    }

    public function getMigrationFile()
    {
        return "";
    }
}
