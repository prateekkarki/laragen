<?php
namespace Prateekkarki\Laragen\Models\Types\File;
use Prateekkarki\Laragen\Models\TypeResolver;
use Prateekkarki\Laragen\Models\Types\FileType;
use Illuminate\Support\Str;

class MultipleType extends FileType
{
    protected $hasModel = true;
    protected $relationalType = true;
    protected $hasMultipleFiles = true;
    protected $formType = 'multipleFiles';
    protected $stubs = [
        'modelMethod' => 'common/Models/fragments/belongsTo',
        'foreignMethod' => 'common/Models/fragments/hasMany'
    ];
    
    public function getPivotSchema()
    {
        $modelName = $this->getParentModelLowercase();
        $moduleName = $this->getParentModule();
        $schema = '$table->bigInteger("'.$modelName.'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$modelName."_id')->references('id')->on('".$moduleName."')->onDelete('set null');".PHP_EOL.$this->getTabs(3);
        $schema .= '$table->string("filename", 192);'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->integer("size");'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->timestamps();'.PHP_EOL.$this->getTabs(3);
        return $schema;
    }

    public function getPivotTable()
    {
        return $this->getParentModelLowercase()."_".strtolower(Str::plural($this->columnName));
    }

    public function getMigrationPivot()
    {
        return $this->getParentModel().Str::plural($this->getChildModel());
    }

    public function getRelatedModel()
    {
        return $this->getPivot();
    }

    public function getPivot()
    {
        return $this->getParentModel().$this->getChildModel();
    }
    
    public function getPivotColumns()
    {
        $columnModels = [];
        foreach ($this->getLaragenColumns() as $column => $optionString) {
            $data = new TypeResolver($this->getPivotTable(), $column, $optionString);
            $columnModels[$column] = $data->getLaragenType();
        }
        return $columnModels;
    }

    public function getTypeColumns()
    {
        return [$this->getParentModelLowercase().'_id', 'filename', 'size'];
    }
}
