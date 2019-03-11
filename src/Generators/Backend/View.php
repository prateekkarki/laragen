<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\DataOption;

class View extends BaseGenerator implements GeneratorInterface
{    
    protected static $initializeFlag = 0;

    public function generate()
    {
        
		$viewsToBeGenerated = ['index']; // To be generated dynamically

		$generatedFiles = [];
		
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('backend/views/' . $view, [
				'{{headings}}' 			 => $this->getHeadings(),
                '{{modelNameLowercase}}' => str_singular($this->module->getModuleName()),
                '{{moduleName}}'         => $this->module->getModuleName()
            ]);

            $fullFilePath = $this->getPath("resources/views/backend/" . $this->module->getModuleName()) . "/{$view}.blade.php";
            file_put_contents($fullFilePath, $viewTemplate);
            $generatedFiles[] =  $fullFilePath;
        }

        $mainMenuFile = $this->getPath("resources/views/backend/includes/")."main_menu.blade.php";

        if(self::$initializeFlag++ == 0){
            $this->initializeFiles([
                $mainMenuFile => "backend/views/includes/main_menu",
            ]);
        }

        $this->insertIntoFile(
            $mainMenuFile,
            '{{-- Main Menu --}}',
			"\n".'<li class="nav-item"><a class="nav-link" href="{{ route("backend.'.$this->module->getModuleName().'.index") }}">'.str_plural($this->module->getModelName()).'</a></li>'
		);
        $generatedFiles = array_merge($generatedFiles, $this->formGenerateCreate());
        return $generatedFiles;
	}

	public function getHeadings(){
        $columns = $this->module->getBackendColumnTitles();
        $headings= "";
        foreach ($columns as $column) {
            $headings .= "<th>".$column."</th>";
        }
		return $headings;
	}

    public function formGenerateCreate()
    {
        $viewTemplate = '';
        foreach($this->module->getData() as $column => $options){
            $columnOptions = new DataOption($column, $options);
            $type = $columnOptions->getType();
            $viewTemplate .= $this->buildTemplate('backend/views/formelements/'.$type, [
                '{{key}}'          => $column,
                '{{display}}'      => $columnOptions->getDisplay(),
                '{{options}}'      => $columnOptions->getFormOptions(),
                '{{modelNameLowercase}}' => strtolower($this->module->getModelName())
            ]);
        }

        $createTemplate = $this->buildTemplate('backend/views/create', [
            '{{moduleName}}'                 => $this->module->getModuleName(),
        ]);

        $editTemplate = $this->buildTemplate('backend/views/edit', [
            '{{moduleName}}'                 => $this->module->getModuleName(),
            '{{modelNameLowercase}}'         => strtolower($this->module->getModelName())
        ]);

        $formTemplate = $this->buildTemplate('backend/views/formelements/_form', [
            '{{createElements}}'             => $viewTemplate,
        ]);

        $editFilePath = $this->getPath("resources/views/backend/" . $this->module->getModuleName()) . "/edit.blade.php";
        file_put_contents($editFilePath, $editTemplate);

        $createFilePath = $this->getPath("resources/views/backend/" . $this->module->getModuleName()) . "/create.blade.php";
        file_put_contents($createFilePath, $createTemplate);
        
        $formFilePath = $this->getPath("resources/views/backend/" . $this->module->getModuleName()) . "/_form.blade.php";
        file_put_contents($formFilePath, $formTemplate);

        $generatedFiles[] =  $createFilePath;
        $generatedFiles[] =  $formFilePath;
        $generatedFiles[] =  $editFilePath;

        return $generatedFiles;

    }
}
