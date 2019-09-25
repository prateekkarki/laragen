<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Illuminate\Support\Str;
use Prateekkarki\Laragen\Models\LaragenOptions;
use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Seeder extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;

    protected $specialTypesToDefinition = [
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
        'long_description'  => 'realText(192)',
        'description'       => 'realText(120)',
        'content'           => 'realText(192)',
    ];

    protected $typeToDefinition = [
        'string'    => 'sentence',
        'integer'   => 'randomNumber()',
        'text'      => 'realText(250)',
        'boolean'   => 'numberBetween(0,1)',
        'date'      => 'date',
        'datetime'  => 'dateTime',
    ];

    public function generate()
    {
        $generatedFiles = [];

        if ($this::$initializeFlag == 0) {
            $laragen = LaragenOptions::getInstance();
            $modules = $laragen->getModules();
            $permissions = [];
            $editPermissions = [];
            $viewPermissions = [];
            foreach ($modules as $module) {
                $permissions[] = 'create_'.$module->getModuleName();
                $permissions[] = 'view_'.$module->getModuleName();
                $permissions[] = 'edit_'.$module->getModuleName();
                $permissions[] = 'delete_'.$module->getModuleName();
                foreach ($module->getColumns() as $field) {
                    $editPermissions[] = 'edit_'.$module->getModuleName().'_'.$field->getColumnKey();
                    $viewPermissions[] = 'view_'.$module->getModuleName().'_'.$field->getColumnKey();
                }
            }
            $allPermissions = [];
            $allPermissions = array_merge($allPermissions, $permissions, $editPermissions, $viewPermissions);

            $permissionsCode = '';
            foreach ($allPermissions as $permission) {
                $permissionsCode .= "Permission::create(['name' => '".$permission."']);".PHP_EOL.$this->getTabs(2);

            }

            $permissionSeederTemplate = $this->buildTemplate('common/permissionSeeder', [
                '{{permissions}}'     => $permissionsCode,
                '{{viewPermissions}}' => implode("', '", $viewPermissions),
                '{{editPermissions}}' => implode("', '", $editPermissions),
            ]);

            $fullFilePath = $this->getPath("database/factories/")."RolesAndPermissionsSeeder.php";
            file_put_contents($fullFilePath, $permissionSeederTemplate);
            $generatedFiles[] = $fullFilePath;

        }
        $factoryTemplate = $this->buildTemplate('common/Factories/Factory', [
            '{{modelName}}'      => $this->module->getModelName(),
            '{{usedModels}}'     => $this->getUsedModels($this->module->getFilteredColumns(['hasSingleRelation', 'hasPivot'])),
            '{{dataDefinition}}' => $this->getDataDefinition($this->module->getFilteredColumns(['general'])),
            '{{foreignData}}'    => $this->getForeignData($this->module->getFilteredColumns(['hasSingleRelation']))
        ]);

        $fullFilePath = $this->getPath("database/factories/").$this->module->getModelName()."Factory.php";
        file_put_contents($fullFilePath, $factoryTemplate);
        $generatedFiles[] = $fullFilePath;

        foreach ($this->module->getFilteredColumns(['hasPivot']) as $type) {
            $typeTemplate = $this->buildTemplate('common/Factories/Factory', [
                '{{modelName}}'      => $type->getPivot(),
                '{{usedModels}}'     => $this->getUsedModels($type->getFilteredColumns('hasSingleRelation'), $type->getPivot()),
                '{{dataDefinition}}' => "",
                '{{foreignData}}'    => $this->getForeignData($type->getFilteredColumns('hasSingleRelation'))
            ]);

            $fullFilePath = $this->getPath("database/factories/").Str::singular($type->getPivot())."Factory.php";
            file_put_contents($fullFilePath, $typeTemplate);
            $generatedFiles[] = $fullFilePath;
        }

        $generatedFiles[] = $this->updateSeeder();

        return $generatedFiles;
    }

    protected function getUsedModels($types = false, $model = false) {
        $namespace = "App\\Models\\";
        $model = $model ? $namespace.$model : $namespace.$this->module->getModelName();
        $usedModels = "use ".$model.";";

        $classes = [$model];

        foreach ($types as $type) {
            $model = $type->getRelatedModel();
            $class = ($model == 'User') ? config('laragen.options.user_model') : "App\\Models\\".$model;
            if (in_array($class, $classes)) {
                continue;
            }
            $classes[] = $class;
            $usedModels .= PHP_EOL."use ".$class.";";
        }
        return $usedModels;
    }

    protected function getDataDefinition($columns) {
        $dataDefinition = "";

        foreach ($columns as $type) {
            $specialTypes = array_keys($this->specialTypesToDefinition);
            $dataDefinition .= in_array($type->getColumn(), $specialTypes) ?
                $this->getTabs(2)."'{$type->getColumn()}'"." => ".'$faker->'.$this->specialTypesToDefinition[$type->getColumn()] : $this->getTabs(2)."'{$type->getColumn()}'"." => ".'$faker->'.$this->typeToDefinition[$type->getDataType()];
            $dataDefinition .= ",".PHP_EOL;
        }
        return $dataDefinition;
    }

    protected function getForeignData($types) {
        $foreignData = "";

        foreach ($types as $type) {
            if ($type->hasSelfParent()) continue;
            $foreignData .= $this->buildTemplate('common/Factories/fragments/options', [
                '{{parent}}'      => $type->getColumnKey(),
                '{{parentModel}}' => $type->getRelatedModel()
            ]);
            $foreignData .= ",".PHP_EOL;
        }
        return $foreignData;
    }

    protected function updateSeeder() {
        $laragenSeederFile = $this->getPath("database/seeds/")."LaragenSeeder.php";

        if(self::$initializeFlag++ == 0)
            $this->initializeFile($laragenSeederFile, 'common/Seeder');

        $this->insertIntoFile(
            $laragenSeederFile,
            "use Illuminate\Database\Seeder;",
            "use App\Models\\".$this->module->getModelName().";\n",
            false
        );

        $this->insertIntoFile(
            $laragenSeederFile,
            "\n        // End factories",
            "\n".$this->getTabs(2)."factory(".$this->module->getModelName()."::class, ".config('laragen.options.seed_rows').")->create();",
            false
        );

        foreach ($this->module->getFilteredColumns(['needsTableInit']) as $type) {

            $this->insertIntoFile(
                $laragenSeederFile,
                "use Illuminate\Database\Seeder;",
                "use App\Models\\".$type->getPivot().";\n",
                false
            );

            $seedData = PHP_EOL.$this->getTabs(2)."if(".$type->getPivot()."::all()->count()==0){";
            $seedData .= PHP_EOL.$this->getTabs(3)."DB::table('".$type->getPivotTable()."')->insert([";
            foreach ($type->getDbData() as $title) {
                $seedData .= PHP_EOL.$this->getTabs(4);
                $seedData .= "['title' => '".$title."'],";
            }
            $seedData .= PHP_EOL.$this->getTabs(3)."]);";
            $seedData .= PHP_EOL.$this->getTabs(2)."}";

            $this->insertIntoFile(
                $laragenSeederFile,
                $this->getStub('fragments/DatabaseSeederRun'),
                $seedData
            );
        }

        foreach ($this->module->getFilteredColumns(['hasPivot']) as $type) {

            $this->insertIntoFile(
                $laragenSeederFile,
                "use Illuminate\Database\Seeder;",
                "use App\Models\\".$type->getPivot().";\n",
                false
            );

            $this->insertIntoFile(
                $laragenSeederFile,
                "\n        // End factories",
                "\n".$this->getTabs(2)."factory(".$type->getPivot()."::class, " . ((int) config('laragen.options.seed_rows') * 2) . ")->create();",
                false
            );
        }

        return $laragenSeederFile;
    }
}
