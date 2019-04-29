<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;
use Prateekkarki\Laragen\Models\Types\RelationalType;

class GalleryType extends RelationalType
{
    public function getModulename()
    {
        return "";
    }

    public function getPivotSchema($modelName, $moduleName)
    {
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');";
        $schema .= '$table->string("filename", 128);';
        $schema .= '$table->timestamps();';
        return $schema;
    }

    public function getMigrationFile()
    {
        return "";
    }
}