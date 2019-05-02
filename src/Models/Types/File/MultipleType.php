<?php
namespace Prateekkarki\Laragen\Models\Types\File;
use Prateekkarki\Laragen\Models\Types\FileType;
use Illuminate\Support\Str;

class MultipleType extends FileType
{
    protected $hasModel = true;
    public $relationalType = true;

    public function getPivotSchema()
    {
        $modelName = $this->getParentModelLowercase();
        $moduleName = $this->getParentModule();
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');";
        $schema .= '$table->string("filename", 192);';
        $schema .= '$table->integer("size");';
        $schema .= '$table->timestamps();';
        return $schema;
    }
    
    public function getPivotTable()
    {
        return $this->getParentModule() . "_" . strtolower(Str::plural($this->columnName));
    }

    public function getPivot()
    {
        return $this->getParentModel() . $this->getChildModel();
    }
    
    public function getTypeColumns()
    {
        return [$this->getParentModelLowercase().'_id', 'filename', 'size'];
    }
}
