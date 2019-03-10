<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Model extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $modelTemplate = $this->buildTemplate('common/Models/Model', [
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => $this->getMassAssignables(),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);
        
        $fullFilePath = $this->getPath("app/Models/").$this->module->getModelName().".php";
        file_put_contents($fullFilePath, $modelTemplate);
        return $fullFilePath;
    }

    protected function getMassAssignables()
    {
        return "'".implode("', '", $this->module->getNativeColumns())."'";
    }

    protected function getForeignMethods()
    {
        $foreignMethods = "";

        foreach ($this->module->getForeignColumns('parent') as $parents) {
            foreach ($parents as $column => $parent) {
                $foreignMethods .= $this->buildTemplate('common/Models/fragments/parent', [
                    '{{parent}}'      => str_singular($parent),
                    '{{columnName}}'  => str_singular($column),
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

        return $foreignMethods;
    }
}
