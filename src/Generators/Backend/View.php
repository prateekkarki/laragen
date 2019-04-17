<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\DataOption;
use Prateekkarki\Laragen\Models\Module;

class View extends BaseGenerator implements GeneratorInterface
{    
    protected static $initializeFlag = 0;

    public function generate()
    {
        
		$viewsToBeGenerated = ['index', 'create', 'edit'];

		$generatedFiles = [];
		
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('backend/views/'.$view, [
                '{{headings}}' 			 => $this->getHeadings(),
                '{{moduleDisplayName}}'  => $this->module->getModuleDisplayName(),
                '{{modelNameLowercase}}' => str_singular($this->module->getModuleName()),
                '{{modelName}}'          => $this->module->getModelName(),
                '{{moduleName}}'         => $this->module->getModuleName()
            ]);

            $fullFilePath = $this->getPath("resources/views/backend/".$this->module->getModuleName())."/{$view}.blade.php";
            file_put_contents($fullFilePath, $viewTemplate);
            $generatedFiles[] = $fullFilePath;
        }

        $mainMenuFile = $this->getPath("resources/views/backend/includes/")."main_menu.blade.php";

        if (self::$initializeFlag++ == 0) {
            $this->initializeFiles([
                $mainMenuFile => "backend/views/includes/main_menu",
            ]);
        }

        

        $this->insertIntoFile(
            $mainMenuFile,
            '{{-- Main Menu --}}',
			"\n".'<li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown" data-toggle="dropdown"><i class="fas fa-columns"></i> <span> '.str_plural($this->module->getModelName()).' </span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="{{ route("backend.'.$this->module->getModuleName().'.create") }}"> Add new '.str_plural($this->module->getModelName()).'</a></li>
                        <li><a class="nav-link" href="{{ route("backend.'.$this->module->getModuleName().'.index") }}">All '.str_plural($this->module->getModelName()).'</a></li>
                    </ul>
                </li>'
		);
        $generatedFiles = array_merge($generatedFiles, $this->formGenerateCreate());
        return $generatedFiles;
	}

	public function getHeadings() {
        $columns = $this->module->getBackendColumnTitles();
        $headings = "";
        foreach ($columns as $column) {
            $headings .= "<th>".$column."</th>";
        }
		return $headings;
	}

    public function formGenerateCreate()
    {
        $viewTemplate = '';
        foreach ($this->module->getData() as $column => $options) {
            $columnOptions = new DataOption($column, $options);
            $type = $columnOptions->getType();
            $viewTemplate .= $this->buildTemplate('backend/views/formelements/'.$type, [
                '{{key}}'                   => $column,
                '{{display}}'               => $columnOptions->getDisplay(),
                '{{options}}'               => $columnOptions->getFormOptions(),
                '{{parentModule}}'          => $columnOptions->getParentModule(),
                '{{parentModuleSinglular}}' => str_singular($columnOptions->getParentModule()),
                '{{parentDisplay}}'         => $this->getParentDisplay($columnOptions->getParentModule()),
                '{{modelNameLowercase}}'    => $this->module->getModelNameLowercase()
            ]);
        }

        $formTemplate = $this->buildTemplate('backend/views/formelements/_form', [
            '{{createElements}}'             => $viewTemplate,
        ]);

        $formFilePath = $this->getPath("resources/views/backend/".$this->module->getModuleName())."/_form.blade.php";
        file_put_contents($formFilePath, $formTemplate);

        $generatedFiles[] = $formFilePath;
        return $generatedFiles;

    }

    public function getParentDisplay($parentModule)
    {
        $modules = config('laragen.modules');
        $displayColumn = "";
        if (isset($modules[$parentModule])) {
            $module = $modules[$parentModule];
            $module = new Module($parentModule, $module);
            $displayColumn = $module->getDisplayColumn();
        }

        if ($parentModule == 'users') {
            $displayColumn = 'name';
        }
        return $displayColumn;
    }
}
