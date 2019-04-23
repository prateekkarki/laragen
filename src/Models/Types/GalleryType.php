<?php
namespace Prateekkarki\Laragen\Models\Types;

class GalleryType extends LaragenType
{
    public $isRelational = true;

    public function getModulename()
    {
        return "";
    }

    public function getTableSchema($modelName, $moduleName)
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
