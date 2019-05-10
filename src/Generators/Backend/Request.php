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
            '{{rules}}' 		=> $this->getRules()
        ]);
        
        $fullFilePath = $this->getPath("app/Http/Requests/Backend/").$this->module->getModelName()."Request".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        return $fullFilePath;
	}
	
    protected function getRules()
    {
        $validation = [];
        $modelname = $this->module->getModelNameLowercase();

        foreach($this->module->getColumns(true) as $column){
            $type = $column->getDataType();
            // $rules = $column->getRules();
            $rules = [];

            $valid_types = [
                'text' => 'string',
                'datetime' => 'date_format:Y-m-d H:i:s',
            ];

            if(array_key_exists($type, $valid_types)){
                $type = $valid_types[$type];
            }

            if ($column->isUnique()) {
                $uniqueValidation = '\''.$column->getColumn().'\' => ($this->route()->'.$modelname.') ? ';
                $uniqueValidation .= '\''.TypeResolver::COLUMN_UNIQUE.':'.$this->module->getModulename().','.$column->getColumn().','.'\''.'.$this->route()->'.$modelname.'->id';
                $uniqueValidation .= ':\''.TypeResolver::COLUMN_UNIQUE.':'.$this->module->getModulename().'\'';
                $validation[]= $uniqueValidation;
            } else {
                $validationLine = ($type == TypeResolver::TYPE_PARENT) ? "'" . $column->getColumn() . "' => 'integer" : "'" . $column->getColumn() . "' => '" . $type;
                $validationLine .= empty($rules) ? "'" : "|" . implode('|', $rules) . "'";
                $validation[]= $validationLine;
            }
        }
        $delimiter = ",\n{$this->getTabs(3)}";
        return (implode($delimiter, $validation));
	}
}
