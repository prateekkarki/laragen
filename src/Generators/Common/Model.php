<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Models\Module;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Model extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $generatedFiles = [];
        $modelTemplate = $this->buildTemplate('common/Models/Model', [
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => $this->getMassAssignables(),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);
        
        $fullFilePath = $this->getPath("app/Models/").$this->module->getModelName().".php";
        file_put_contents($fullFilePath, $modelTemplate);
        $generatedFiles[] = $fullFilePath;
        
        foreach ($this->module->getMultipleColumns() as $multipleModules) {
            foreach ($multipleModules as $multiple => $multipleData) {
                $multipleModule = new Module($multiple, $multipleData);
                
                $multiModelTemplate = $this->buildTemplate('common/Models/Model', [
                    '{{modelName}}'       => $this->module->getPivotName($multiple),
                    '{{massAssignables}}' => $this->getMultipleMassAssignables($multipleModule),
                    '{{foreignMethods}}'  => $this->getMultipleForeignMethods()
                ]);

                $fullFilePath = $this->getPath("app/Models/").$this->module->getPivotName($multiple).".php";
                file_put_contents($fullFilePath, $multiModelTemplate);
                $generatedFiles[] = $fullFilePath;
            }
        }

        return $generatedFiles;
    }

    protected function getMassAssignables()
    {
        $columns = array_merge($this->module->getNativeColumns(), $this->module->getFileColumns(), $this->module->getParentColumns());
        return "'".implode("', '", $columns)."'";
    }

    protected function getMultipleMassAssignables($multipleModule)
    {
        $columns = array_merge($multipleModule->getNativeColumns(), $multipleModule->getFileColumns(), $multipleModule->getParentColumns());
        return "'".implode("', '", $columns)."'";
    }

    protected function getMultipleForeignMethods()
    {
        $foreignMethods = "";

        $foreignMethods .= $this->buildTemplate('common/Models/fragments/multiple', [
            '{{parent}}'      => $this->module->getModelNameLowercase(),
            '{{parentModel}}' => $this->module->getModelName()
        ]);

        return $foreignMethods;
    }

    protected function getForeignMethods()
    {
        $foreignMethods = "";

        foreach ($this->module->getForeignColumns('parent') as $parents) {
            foreach ($parents as $column => $parent) {
                $foreignMethods .= $this->buildTemplate('common/Models/fragments/parent', [
                    '{{parent}}'      => str_singular($parent),
                    '{{columnName}}'  => $column,
                    '{{parentModel}}' => ($parent == 'users' && class_exists('\\App\\User')) ? "\\App\\User" : ucfirst(camel_case(str_singular($parent)))
                ]);
            }
        }

        foreach ($this->module->getForeignColumns('related') as $relatedModels) {
            foreach ($relatedModels as $column => $relatedModel) {
                $foreignMethods .= $this->buildTemplate('common/Models/fragments/related', [
                    '{{related}}'      => str_singular($relatedModel),
                    '{{columnName}}'  => $column,
                    '{{relatedModel}}' => ($relatedModel == 'users' && class_exists('\\App\\User')) ? "\\App\\User" : ucfirst(camel_case(str_singular($relatedModel)))
                ]);
            }
        }


        foreach ($this->module->getMultipleColumns() as $multipleModules) {
            foreach ($multipleModules as $multiple => $multipleData) {
                $foreignMethods .= $this->buildTemplate('common/Models/fragments/multiple_relation', [
                    '{{related}}'      => str_singular($multiple),
                    '{{columnName}}'  => str_plural($multiple),
                    '{{relatedModel}}' => $this->module->getPivotName($multiple)
                ]);
            }
        }

        return $foreignMethods;
    }
}
