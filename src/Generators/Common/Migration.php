<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Migration extends BaseGenerator implements GeneratorInterface
{
    protected static $counter = 0;

    private static $destination = "laragen/database/migrations";
    private static $tableTemplate = "common/migrations/table";
    private static $pivotTemplate = "common/migrations/pivot";

    public function generate()
    {
        if (self::$counter == 0 && is_dir(base_path(self::$destination."/"))) {
            $this->deleteFiles(base_path(self::$destination."/"));
        }

        $generatedFiles = [];
        foreach ($this->module->getFilteredColumns('needsTableInit') as $type) {
            $migrationTemplate = $this->buildTemplate(self::$pivotTemplate, [
                '{{pivotName}}'        => $type->getMigrationPivot(),
                '{{pivotTableName}}'   => $type->getPivotTable(),
                '{{pivotTableSchema}}' => $type->getPivotSchema()
            ]);

            $fullFilePath = $this->getPivotFile($type);
            file_put_contents($fullFilePath, $migrationTemplate);
            $generatedFiles[] = $fullFilePath;
        }

        $migrationTemplate = $this->buildTemplate(self::$tableTemplate, [
            '{{modelName}}'         => $this->module->getModelName(),
            '{{modelNamePlural}}'   => $this->module->getModelNamePlural(),
            '{{moduleName}}'        => $this->module->getModuleName(),
            '{{modelTableSchema}}'  => $this->getSchema()
        ]);

        $fullFilePath = $this->getMigrationFile();
        file_put_contents($fullFilePath, $migrationTemplate);
        $generatedFiles[] = $fullFilePath;

        foreach ($this->module->getFilteredColumns(['hasPivot']) as $type) {
            $migrationTemplate = $this->buildTemplate(self::$pivotTemplate, [
                '{{pivotName}}'        => $type->getMigrationPivot(),
                '{{pivotTableName}}'   => $type->getPivotTable(),
                '{{pivotTableSchema}}' => $type->getPivotSchema()
            ]);

            $fullFilePath = $this->getPivotFile($type);
            file_put_contents($fullFilePath, $migrationTemplate);
            $generatedFiles[] = $fullFilePath;
        }
        return $generatedFiles;
    }

    protected function getMigrationFile()
    {
        $fileCounter = sprintf('%06d', (int) date('His') + ++self::$counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".$this->module->getModuleName()."_table.php";

        return $this->getPath(self::$destination."/").$filenamePrefix.$fileName;
    }

    protected function getPivotFile($related)
    {
        $fileCounter = sprintf('%06d', (int) date('His') + ++self::$counter);
        $filenamePrefix = date('Y_m_d_').$fileCounter."_";
        $fileName = "create_".$related->getPivotTable()."_table.php";

        return $this->getPath(self::$destination."/").$filenamePrefix.$fileName;
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
