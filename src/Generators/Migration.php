<?php

namespace Prateekkarki\Laragen\Generators;

use Prateekkarki\Laragen\Models\Module;
use Prateekkarki\Laragen\Models\DataOption;

class Migration extends BaseGenerator implements GeneratorInterface
{
    protected static $counter = 0;

    public function generate()
    {
        $migrationTemplate = $this->buildTemplate('Migration', [
            '{{modelName}}'         => $this->module->getModelName(),
            '{{modelNamePlural}}'   => $this->module->getModelNamePlural(),
            '{{moduleName}}'        => $this->module->getModuleName(),
            '{{modelTableSchema}}'  => $this->getSchema()
        ]);
        
        $fileCounter = (int)date('His') + ++self::$counter;
        $filenamePrefix = date('Y_m_d_') . $fileCounter . "_";
        $fileName = "create_" . $this->module->getModuleName() . "_table.php";

        $existingFiles = scandir($this->getPath("database/migrations/"));
        
        foreach ($existingFiles as $file) {
            if (strpos($file, $fileName) !== false) {
                $filenamePrefix = str_replace($fileName, "", $file);
            }
        }

        $fullFilePath = $this->getPath("database/migrations/") . $filenamePrefix . $fileName;  
        file_put_contents($fullFilePath, $migrationTemplate);

        return $fullFilePath;
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
                $schema .= PHP_EOL . $this->getTabs(3);
            }
        }

        return $schema;
    }
}
