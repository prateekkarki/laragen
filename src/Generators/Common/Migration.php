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

        foreach ($this->module->getGalleries() as $gallery) {
            $pivotTemplate = $this->buildTemplate('common/migrations/pivot', [
                '{{pivotName}}'         => $this->module->getPivotName($gallery),
                '{{pivotTableName}}'    => $this->module->getPivotTableName($gallery),
                '{{pivotTableSchema}}'  => $this->getGallerySchema($gallery)
            ]);
            $pivotFilePath = $this->getPivotFile($gallery);
            file_put_contents($pivotFilePath, $pivotTemplate);
            $generatedFiles[] = $pivotFilePath;
        }

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

        foreach ($this->module->getMultipleColumns() as $multipleModules) {
            foreach ($multipleModules as $multiple => $multipleData) {
                $pivotTemplate = $this->buildTemplate('common/migrations/pivot', [
                    '{{pivotName}}'         => str_singular($this->module->getPivotName($multiple)),
                    '{{pivotTableName}}'    => str_plural($this->module->getPivotTableName($multiple)),
                    '{{pivotTableSchema}}'  => $this->getMultipleSchema($multipleData)
                ]);
                
                $pivotFilePath = $this->getPivotFile($multiple);
                file_put_contents($pivotFilePath, $pivotTemplate);
                $generatedFiles[] = $pivotFilePath;
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

    protected function getMultipleSchema($multipleData)
    {
        $schema = '$table->bigInteger("'.$this->module->getModelNameLowercase().'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$this->module->getModelNameLowercase()."_id')->references('id')->on('".$this->module->getModulename()."')->onDelete('set null');";

        foreach ($multipleData as $column => $optionString) {
            $option = new DataOption($column, $optionString);
            $schema .= $option->getSchema();
            $schema .= ''.PHP_EOL.$this->getTabs(3);
        }
        return $schema;
    }

    protected function getPivotSchema($related)
    {
        $schema = '$table->bigInteger("'.$this->module->getModelNameLowercase().'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$this->module->getModelNameLowercase()."_id')->references('id')->on('".$this->module->getModulename()."')->onDelete('set null');";

        $schema .= '$table->bigInteger("'.str_singular($related).'_id")->unsigned()->nullable();';
        $schema .= "\$table->foreign('".str_singular($related)."_id')->references('id')->on('".$related."')->onDelete('set null');";

        return $schema;
    }

    protected function getGallerySchema($gallery)
    {
        $schema = '$table->bigInteger("'.$this->module->getModelNameLowercase().'_id")->unsigned()->nullable();'.PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".$this->module->getModelNameLowercase()."_id')->references('id')->on('".$this->module->getModulename()."')->onDelete('set null');";
        $schema .= '$table->string("filename", 128);';
        $schema .= '$table->timestamps();';
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
