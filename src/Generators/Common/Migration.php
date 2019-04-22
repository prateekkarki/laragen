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
                $migrationTemplate = $this->buildTemplate('common/migrations/table', [
                    '{{modelName}}'         => $this->module->getModelName(),
                    '{{modelNamePlural}}'   => $this->module->getModelNamePlural(),
                    '{{moduleName}}'        => $this->module->getModuleName(),
                    '{{modelTableSchema}}'  => $related->getSchema($this->module->getModelNameLowerCase(), $this->module->getModuleName())
                ]);
                
                // $fullFilePath = $related->getMigrationFile();
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

        $existingFiles = scandir($this->getPath("database/migrations/"));
        
        foreach ($existingFiles as $file) {
            if (strpos($file, $fileName) !== false) {
                $filenamePrefix = str_replace($fileName, "", $file);
            }
        }
        return $this->getPath("database/migrations/").$filenamePrefix.$fileName;
    }

    protected function getPivotFile($related)
    {
        $fileCounter = sprintf('%06d', (int) date('His') + ++self::$counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".str_singular($this->module->getPivotTableName($related))."_table.php";

        $existingFiles = scandir($this->getPath("database/migrations/"));
        
        foreach ($existingFiles as $file) {
            if (strpos($file, $fileName) !== false) {
                $filenamePrefix = str_replace($fileName, "", $file);
            }
        }
        
        return $this->getPath("database/migrations/").$filenamePrefix.$fileName;
    }

    protected function getSchema()
    {
        $schema = "";
        $keyArray = array_keys($this->module->getData());
        $lastColumn = array_pop($keyArray);

        foreach ($this->module->getData() as $column => $optionString) {
            $option = new DataOption($column, $optionString);

            $schema .= $option->getSchema();

            if ($column != $lastColumn) {
                $schema .= PHP_EOL.$this->getTabs(3);
            }
        }

        return $schema;
    }
}
