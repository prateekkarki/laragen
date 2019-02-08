<?php

namespace Prateekkarki\Laragen\Generators;

use Prateekkarki\Laragen\Models\Module;

class Seeder extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $generatedFiles = [];
        $modelTemplate = $this->buildTemplate('Factory', [
            '{{usedModels}}'     => $this->module->getModelName(),
            '{{modelName}}'      => $this->getMassAssignables(),
            '{{dataDefinition}}' => $this->getMassAssignables(),
            '{{foreignData}}'    => $this->getForeignMethods()
        ]);
        $fullFilePath = $this->getPath("database/factories/") . $this->module->getModelName() . "Factory.php";
        file_put_contents($fullFilePath, $modelTemplate);

        
        $modelTemplate = $this->buildTemplate('Seeder', [
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => $this->getMassAssignables(),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);
        $fullFilePath = $this->getPath("database/seeds/") . $this->module->getModelName() . "Seeder.php";
        file_put_contents($fullFilePath, $modelTemplate);

        
        $generatedFiles[] =  $fullFilePath;
        return $generatedFiles;         
    }
}
