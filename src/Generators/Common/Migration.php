<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\DataOption;

class Migration extends BaseGenerator implements GeneratorInterface
{
    protected static $counter = 0;

    public function generate()
    {
        $generatedFiles = [];

        $migrationTemplate = $this->buildTemplate('common/migrations/table', [
            '{{modelName}}'         => $this->module->getModelName(),
            '{{modelNamePlural}}'   => $this->module->getModelNamePlural(),
            '{{moduleName}}'        => $this->module->getModuleName(),
            '{{modelTableSchema}}'  => $this->getSchema()
        ]);
        
        $fullFilePath = $this->getMigrationFile();
        file_put_contents($fullFilePath, $migrationTemplate);
        $generatedFiles[] = $fullFilePath;

        if($this->module->hasRelations()){
            foreach($this->module->relativeTypes as $related){
                $migrationTemplate = $this->buildTemplate('common/migrations/pivot', [
                    '{{pivotName}}'         => $related->getPivotName($this->module->getModelName()),
                    '{{pivotTableName}}'   => $related->getPivotTableName($this->module->getModelNameLowerCase()),
                    '{{pivotTableSchema}}'  => $related->getTableSchema($this->module->getModelNameLowerCase(), $this->module->getModuleName())
                ]);
                
                $fullFilePath = $this->getPath("database/migrations/laragen/") . $related->getPivotFile($this->module->getModelNameLowerCase(), ++self::$counter);
                file_put_contents($fullFilePath, $migrationTemplate);
                $generatedFiles[] = $fullFilePath;
            }
        }
        return $generatedFiles;
    }

    protected function getMigrationFile()
    {
        $fileCounter = sprintf('%06d', (int) date('His') + ++self::$counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".$this->module->getModuleName()."_table.php";

        return $this->getPath("database/migrations/laragen/").$filenamePrefix.$fileName;
    }

    protected function getPivotFile($related)
    {
        $fileCounter = sprintf('%06d', (int) date('His') + ++self::$counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".str_singular($this->module->getPivotTableName($related))."_table.php";

        return $this->getPath("database/migrations/laragen/").$filenamePrefix.$fileName;
    }

    protected function getSchema()
    {
        $schema = "";
        foreach ($this->module->getColumns(true) as $type) {

            $schema .= $type->getSchema();
            
            if ($type->getColumn() != $this->module->getLastColumn()) {
                $schema .= PHP_EOL.$this->getTabs(3);
            }
        }

        return $schema;
    }
}
