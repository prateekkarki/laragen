<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Request extends BaseGenerator implements GeneratorInterface
{
    protected $destination = "laragen/app/Http/Requests/Backend";
    protected $namespace  = "Laragen\App\Http\Requests\Backend";
    protected $template  = "backend/Request";
    protected $fileSuffix  = "Request";

    protected $childDestination = "app/Http/Requests/Backend";
    protected $childNamespace  = "App\Http\Requests\Backend";

    public function generate()
    {
        $requestTemplate = $this->buildTemplate($this->template, [
            '{{namespace}}'     => $this->namespace,
            '{{modelName}}'     => $this->module->getModelName(),
            '{{moduleName}}'    => $this->module->getModuleName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
            '{{rules}}' 		=> $this->getRules(),
        ]);

        return $this->generateFile($requestTemplate);
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
