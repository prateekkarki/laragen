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

        foreach ($this->module->getForeignColumns('related') as $relatedModules) {
            foreach ($relatedModules as $related) {
                $pivotTemplate = $this->buildTemplate('common/migrations/pivot', [
                    '{{pivotName}}'         => $this->module->getPivotName($related),
                    '{{pivotTableName}}'    => $this->module->getPivotTableName($related),
                    '{{pivotTableSchema}}'  => $this->getPivotSchema($related)
                ]);
            }
            $pivotFilePath = $this->getPivotFile($related);
            file_put_contents($pivotFilePath, $pivotTemplate);
            $generatedFiles[] = $pivotFilePath;
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
        $fileName = "create_".$this->module->getPivotTableName($related)."_table.php";

        $existingFiles = scandir($this->getPath("database/migrations/"));
        
        foreach ($existingFiles as $file) {
            if (strpos($file, $fileName) !== false) {
                $filenamePrefix = str_replace($fileName, "", $file);
            }
        }
        
        return $this->getPath("database/migrations/").$filenamePrefix.$fileName;
    }

    protected function getPivotSchema($related)
    {
        $schema =  '$table->integer("'.$this->module->getModelNameSingularLowercase().'_id")->unsigned();'.PHP_EOL.$this->getTabs(3);
        $schema .= '$table->integer("'.str_singular($related).'_id")->unsigned();';
        return $schema;
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
