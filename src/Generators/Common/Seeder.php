<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Illuminate\Support\Str;

class Seeder extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;

    public function generate()
    {
        $generatedFiles = [];
        $factoryTemplate = $this->buildTemplate('common/Factories/Factory', [
            '{{modelName}}'      => $this->module->getModelName(),
            '{{usedModels}}'     => $this->getUsedModels(),
            '{{dataDefinition}}' => $this->getDataDefinition($this->module->getFilteredColumns(['general'])),
            '{{foreignData}}'    => $this->getForeignData()
        ]);

        $fullFilePath = $this->getPath("database/factories/").$this->module->getModelName()."Factory.php";
        file_put_contents($fullFilePath, $factoryTemplate);
        $generatedFiles[] = $fullFilePath;

        foreach($this->module->getFilteredColumns(['hasPivot', 'hasModel']) as $type){
            $typeTemplate = $this->buildTemplate('common/Factories/Factory', [
                '{{modelName}}'      => $type->getRelatedModel(),
                '{{usedModels}}'     => "",
                '{{dataDefinition}}' => "'title' => \$faker->realText(20)",
                // '{{dataDefinition}}' => $this->getDataDefinition($type->getRelatedModel()),
                '{{foreignData}}'    => ""
            ]);
            
            $fullFilePath = $this->getPath("database/factories/").Str::singular($type->getPivot())."Factory.php";
            file_put_contents($fullFilePath, $typeTemplate);
            $generatedFiles[] = $fullFilePath;
        }
        
        $generatedFiles[] = $this->updateSeeder();
        
        return $generatedFiles;         
    }

    protected function getUsedModels() {
        $namespace = "App\\Models\\";
        $usedModels = "use ".$namespace.$this->module->getModelName().";";

        $classes = [$namespace.$this->module->getModelName()];
        foreach($this->module->getFilteredColumns(['hasSingleRelation']) as $type){
            $module = $type->getRelatedModel();
            $namespace = ($module == 'User' && class_exists('App\\User')) ? "App\\" : "App\\Models\\";
            $class = $namespace;
            $class .= $type->getRelatedModel();

            if(in_array($class, $classes)){
                continue;
            }
            $classes[] = $class;
            $usedModels .= PHP_EOL."use ".$class.";";
        }
        return $usedModels;
    }

    protected function getDataDefinition($columns) {
        $specialTypesToDefinition = [
            'title'             => 'realText(20)',
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
            'sort'              => 'numberBetween(0,20)',
            'short_description' => 'realText(150)',
            'long_description'  => 'realText(500)',
            'description'       => 'realText(500)',
            'content'           => 'realText(500)',
        ];

        $typeToDefinition = [
            'string'    => 'sentence',
            'integer'   => 'randomNumber()',
            'text'      => 'realText(500)',
            'boolean'   => 'numberBetween(0,1)',
            'date'      => 'date',
            'datetime'  => 'dateTime',
        ];

        $dataDefinition = "";

        foreach ($columns as $type) {
            $specialTypes = array_keys($specialTypesToDefinition);
            if (in_array($type->getColumn(), $specialTypes)) {
                $dataDefinition .= $this->getTabs(2)."'{$type->getColumn()}'"." => ".'$faker->'.$specialTypesToDefinition[$type->getColumn()];
            } else {
                $dataDefinition .= $this->getTabs(2)."'{$type->getColumn()}'"." => ".'$faker->'.$typeToDefinition[$type->getDataType()];
            }

            if ($type != last($columns)) {
                $dataDefinition .= ",".PHP_EOL;
            }
        }
        return $dataDefinition;
    }

    protected function getForeignData() {
        $foreignData = "";

        foreach($this->module->getFilteredColumns(['hasSingleRelation']) as $type){
            $foreignData .= $this->buildTemplate('common/Factories/fragments/options', [
                '{{parent}}'      => $type->getcolumn(),
                '{{parentModel}}' => $type->getRelatedModel()
            ]);
            
            if ($type != last($type)) {
                $foreignData .= ",".PHP_EOL;
            }
        }
        return $foreignData;
    }

    protected function updateSeeder() {
        $laragenSeederFile = (self::$initializeFlag++ == 0) ? $this->initializeFile($this->getPath("database/seeds/")."LaragenSeeder.php", 'common/Seeder') : $this->getPath("database/seeds/")."LaragenSeeder.php";

        $this->insertIntoFile(
            $laragenSeederFile,
            "use Illuminate\Database\Seeder;",
            "use App\Models\\".$this->module->getModelName().";\n",
            false
        );

        $this->insertIntoFile(
            $laragenSeederFile,
            "\n        // End factories",
            "\n".$this->getTabs(2)."factory(".$this->module->getModelName()."::class, 25)->create();",
            false
        );

        foreach($this->module->getFilteredColumns(['needsTableInit']) as $type){
            
            $this->insertIntoFile(
                $laragenSeederFile,
                "use Illuminate\Database\Seeder;",
                "use App\Models\\".$type->getPivot().";\n",
                false
            );

            $seedData = PHP_EOL.$this->getTabs(2). "if(".$type->getPivot()."::all()->count()==0){";
            $seedData .= PHP_EOL.$this->getTabs(3). "DB::table('".$type->getPivotTable()."')->insert([";
            foreach($type->getDbData() as $title){
                $seedData .= PHP_EOL.$this->getTabs(4);
                $seedData .= "['title' => '" . $title . "'],";
            }
            $seedData .=  PHP_EOL.$this->getTabs(3). "]);";
            $seedData .=  PHP_EOL.$this->getTabs(2). "}";

            $this->insertIntoFile(
                $laragenSeederFile,
                $this->getStub('fragments/DatabaseSeederRun'),
                $seedData
            );
        }

        return $laragenSeederFile;
    }
}
