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
            '{{authorization}}' => "true",
            '{{rules}}' 		=> $this->getRules()
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Requests/Backend/").$this->module->getModelName()."Request".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
	}
	
    protected function getRules()
    {
        $validation = [];
        $moduleData =$this->module->getData();
        $modelname = $this->module->getModelNameLowercase();

        foreach($moduleData as $column => $options){
            $columnOptions = new TypeResolver($this->module->getModuleName(), $column, $options);
            $type = $columnOptions->laragenType->getDataType();
            $rules = $columnOptions->laragenType->optionArray();

            $valid_types = [
                'text' => 'string',
                'datetime' => 'date_format:Y-m-d H:i:s',
            ];

            if(array_key_exists($type, $valid_types)){
                $type = $valid_types[$type];
            }

            if(in_array($type, TypeResolver::$fileTypes) || ($type == TypeResolver::TYPE_RELATED)) continue;

            $uniqueValidation = '\''.$column.'\' => ($this->route()->'.$modelname.') ? ';
            $uniqueValidation .= '\''.TypeResolver::COLUMN_UNIQUE.':'.$this->module->getModulename().','.$column.','.'\''.'.$this->route()->'.$modelname.'->id';
            $uniqueValidation .= ':\''.TypeResolver::COLUMN_UNIQUE.':'.$this->module->getModulename().'\'';

            if ($columnOptions->laragenType->isUnique()) {
                $validation[]= $uniqueValidation;
            } else {
                $validationLine = ($type == TypeResolver::TYPE_PARENT) ? "'" . $column . "' => 'integer" : "'" . $column . "' => '" . $type;
                $validationLine .= empty($rules) ? "'" : "|" . implode('|', $rules) . "'";
                $validation[]= $validationLine;
            }
        }
        $delimiter = ",\n{$this->getTabs(3)}";
        return (implode($delimiter, $validation));
	}
}
