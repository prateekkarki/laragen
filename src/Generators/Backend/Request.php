<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Request extends BaseGenerator implements GeneratorInterface
{
    private static $destination = "laragen/app/Http/Requests/Backend";
    private static $namespace  = "Laragen\App\Http\Requests\Backend";
    private static $template  = "backend/Request";

    private static $childDestination = "app/Http/Requests/Backend";
    private static $childNamespace  = "App\Http\Requests\Backend";
    private static $childTemplate  = "backend/EmptyClass";

    public function generate()
    {
        $generatedFiles = [];

        $requestTemplate = $this->buildTemplate(self::$template, [
            '{{namespace}}'     => self::$namespace,
            '{{modelName}}'     => $this->module->getModelName(),
            '{{moduleName}}'    => $this->module->getModuleName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
            '{{rules}}' 		=> $this->getRules(),
        ]);

        $childTemplate = $this->buildTemplate(self::$childTemplate, [
            '{{namespace}}'          => self::$childNamespace,
            '{{className}}'          => $this->module->getModelName()."Request",
            '{{extendsClass}}'       => self::$namespace . '\\' . $this->module->getModelName()."Request"
        ]);

        $fullFilePath = $this->getPath(self::$destination."/").$this->module->getModelName()."Request".".php";
        file_put_contents($fullFilePath, $requestTemplate);
        $generatedFiles[] = $fullFilePath;

        $fullFilePath = $this->getPath(self::$childDestination."/").$this->module->getModelName()."Request".".php";
        file_put_contents($fullFilePath, $childTemplate);
        $generatedFiles[] = $fullFilePath;

        return $generatedFiles;

    }

    protected function getRules()
    {
        $validation = [];
        foreach ($this->module->getColumns(true) as $column) {
            $validation[] = "'{$column->getColumnKey()}'"." => ".$this->getValidationLine($column);
        }
        $delimiter = ",\n{$this->getTabs(3)}";
        return (implode($delimiter, $validation));
    }


    public function getValidationLine($type)
    {
        $validationSegments = [];
        $modelname = $this->module->getModelName();

        $validationSegments[] = $type->isRequired() ? 'required' : 'nullable';
        $validationSegments[] = $type->getValidationRule() ?? $type->getDataType();
        $rules = implode('|', $validationSegments);

        if ($type->isUnique()) {
            $validationLine = '($this->'.$modelname.') ? \'';
            $validationLine .= $rules.'|unique:'.$type->moduleName.','.$type->getColumn().','.'\''.'.$this->'.$modelname.'->id : \'';
            $validationLine .= $rules.'|unique:'.$type->moduleName.'\'';
        } else {
            $validationLine = "'{$rules}'";
        }
        return $validationLine;
    }
}
