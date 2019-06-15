<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Observer extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;
    public function generate()
    {   
        $generatedFiles=[]; 
        
        if($this::$initializeFlag == 0){
            $laragen = app('laragen');
            $modules = $laragen->getModules();
            $models = [];
            foreach ($modules as $module) {
                $models[] = $module->getModelName(); 
            }

            $modelsCode = '';
            $usedClasses = '';
            foreach ($models as $model) {
                $modelsCode .= $model ."::observe(". $model ."Observer::class);" . PHP_EOL. $this->getTabs(2);
                $usedClasses .= "use App\Observers\\". $model . "Observer;" . PHP_EOL;
                $usedClasses .= "use App\Models\\". $model . ";" . PHP_EOL;
            }

            $observerProviderTemplate = $this->buildTemplate('common/LaragenObserverServiceProvider', [
                '{{modelObservers}}'     => $modelsCode,
                '{{usedClasses}}'     => $usedClasses,
            ]);

            $fullFilePath = $this->getPath("app/Providers/")."LaragenObserverServiceProvider.php";
            file_put_contents($fullFilePath, $observerProviderTemplate);
            $generatedFiles[] = $fullFilePath;
            $this::$initializeFlag++;

        }

        $controllerTemplate = $this->buildTemplate('backend/observers/observer', [
            '{{modelName}}'           => $this->module->getModelName(),
            '{{modelNameLowercase}}' => $this->module->getModelNameLowercase(),
        ]);
        
        $fullFilePath = $this->getPath("app/Observers/").$this->module->getModelName()."Observer".".php";
        file_put_contents($fullFilePath, $controllerTemplate);
        $generatedFiles[] = $fullFilePath;
        return $generatedFiles;
    }

}
