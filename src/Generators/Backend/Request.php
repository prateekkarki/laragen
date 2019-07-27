<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\TypeResolver;


class Request extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $controllerTemplate = $this->buildTemplate('backend/Request', [
            '{{modelName}}'     => $this->module->getModelName(),
            '{{moduleName}}'    => $this->module->getModuleName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
            '{{rules}}' 		=> $this->getRules(),
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Requests/Backend/").$this->module->getModelName()."Request".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
	}
	
    protected function getRules()
    {
        $validation = [];
        foreach($this->module->getColumns(true) as $column){
            $validation[] =  "'{$column->getColumnKey()}'" . " => " .  $column->getValidationLine();
        }
        $delimiter = ",\n{$this->getTabs(3)}";
        return (implode($delimiter, $validation));
	}
}