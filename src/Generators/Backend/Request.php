<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\DataOption;


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
            $columnOptions = new DataOption($column, $options);
            $type = $columnOptions->getType();
            $rules = $columnOptions->optionArray();

            // dump($type);
            
            if(empty($rules)) continue;
            if($type=='image') continue;
            if($type=='file') continue;

            foreach ($rules as $r) {
                if(str_contains($r, 'unique')){
                    $containsUnique = true;
                    break;
                }
                else{
                    $containsUnique = false;
                }
            }
            if (isset($containsUnique) && $containsUnique == true) {
                $validation[]= "'".$column."' => ".'($this->route()->'.$modelname.") ? '".$r.",".$column.",'".'.$this->route()->'.$modelname.'->id'." : '".$r."'";
            } else {
                $validation[]= "'".$column."' => '".implode('|',$rules)."'";
            }
        }
        $delimiter = ",\n{$columnOptions->getTabs(3)}";
        return (implode($delimiter, $validation));
	}
}
