<?php
namespace Prateekkarki\Laragen\Models\Types;

class RelationalType extends LaragenType
{
    public $isRelational = true;

    public function getPivotTableName($model= "")
    {
        $moduleArray = [str_singular($model), str_singular($this->columnName)];
        sort($moduleArray);
        return implode("_", $moduleArray);
    }
    
    public function getPivotName($model= "")
    {
        $moduleArray = [ucfirst(str_singular($model)), ucfirst(str_singular($this->columnName))];
        sort($moduleArray);
        return implode("", $moduleArray);
    }

    
    public function getPivotFile($model= "", $counter = 0)
    {
        $fileCounter = sprintf('%06d', (int) date('His') + $counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".str_singular($this->getPivotTableName($model, $this->columnName))."_table.php";

        return $filenamePrefix.$fileName;
    }

}
