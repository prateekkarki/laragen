<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Seeder extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;

    public function generate()
    {

        $generatedFiles = [];
        $factoryTemplate = $this->buildTemplate('Factory', [
            '{{modelName}}'      => $this->module->getModelName(),
            '{{usedModels}}'     => $this->getUsedModels(),
            '{{dataDefinition}}' => $this->getDataDefinition(),
            '{{foreignData}}'    => $this->getForeignData()
        ]);

        $fullFilePath = $this->getPath("database/factories/").$this->module->getModelName()."Factory.php";
        file_put_contents($fullFilePath, $factoryTemplate);
        $generatedFiles[] = $fullFilePath;
        
        $laragenSeederFile = (self::$initializeFlag++ == 0) ? $this->initializeFile($this->getPath("database/seeds/")."LaragenSeeder.php", 'Seeder') :  $this->getPath("database/seeds/")."LaragenSeeder.php";

        $this->insertIntoFile(
            $laragenSeederFile,
            "use Illuminate\Database\Seeder;",
            "use App\Models\\".$this->module->getModelName().";\n",
            false
        );

        $this->insertIntoFile(
            $laragenSeederFile,
            $this->getStub('fragments/DatabaseSeederRun'),
            "\n".$this->getTabs(2)."factory(".$this->module->getModelName()."::class, 5)->create();"
        );

        $generatedFiles[] = $laragenSeederFile;
        
        return $generatedFiles;         
    }

    protected function getUsedModels() {
        $foreignModels = $this->module->getForeignColumns();
        $namespace = "App\\Models\\";
        $usedModels = "use ".$namespace.$this->module->getModelName().";";

        foreach ($foreignModels as $models) {
            foreach ($models as $column => $module) {
                $namespace = ($module == 'users' && class_exists('App\\User')) ? "App\\" : "App\\Models\\";
                $class = $namespace.$this->moduleToModelName($module);
                $usedModels .= PHP_EOL."use ".$class.";";
            }
        }
        return $usedModels;
    }

    protected function getDataDefinition() {
        $specialTypesToDefinition = [
            'title'             => 'realText(50)',
            'firstname'         => 'firstname',
            'lastname'          => 'lastname',
            'name'              => 'name',
            'company'           => 'company',
            'email'             => 'email',
            'streetName'        => 'streetName',
            'streetAddress'     => 'streetAddress',
            'postcode'          => 'postcode',
            'address'           => 'address',
            'country'           => 'country',
            'dateTime'          => 'dateTime',
            'month'             => 'month',
            'year'              => 'year',
            'url'               => 'url',
            'slug'              => 'slug',
            'short_description' => 'realText(150)',
            'long_description'  => 'realText(500)',
            'description'       => 'realText(500)',
            'content'           => 'realText(500)',
        ];

        $typeToDefinition = [
            'string'    => 'sentence',
            'int'       => 'randomNumber()',
            'text'      => 'realText(500)',
            'bool'      => 'numberBetween(0,1)',
            'date'      => 'date',
            'datetime'  => 'dateTime',
        ];

        $dataDefinition = "";
        foreach ($this->module->getNativeData() as $columns) {
            foreach($columns as $column => $type){
                $specialTypes = array_keys($specialTypesToDefinition);
                if(in_array($column,$specialTypes)){
                    $dataDefinition .= $this->getTabs(2) . "'{$column}'" . " => " . '$faker->' . $specialTypesToDefinition[$column];
                } else{
                    $dataDefinition .= $this->getTabs(2) . "'{$column}'" . " => " . '$faker->' . $typeToDefinition[$type];
                }

                if($column != last($columns)) {
                    $dataDefinition .= "," . PHP_EOL;
                }
            }
        }
        return $dataDefinition;
    }

    protected function getForeignData(){
        $columns = $this->module->getForeignColumns('parent');

        $foreignData = "";

        foreach ($columns as $parents) {
            foreach ($parents as $column => $parent) {
                $foreignData .= $this->buildTemplate('Factory-parent', [
                    '{{parent}}'      => str_singular($parent),
                    '{{parentModel}}' => ucfirst(camel_case(str_singular($parent)))
                ]);
                
                if($column != last($columns)) {
                    $foreignData .= "," . PHP_EOL;
                }
            }
        }

        return $foreignData;
    }
}
