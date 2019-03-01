<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class View extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
		$viewsToBeGenerated = ['index']; // To be generated dynamically

		$generatedFiles = [];
		
        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('Backend/Views/' . $view, [
				'{{headings}}' 			 => $this->getHeadings(),
				'{{modelData}}' 		 => $this->getModelData(),
                '{{modelNameLowercase}}' => str_singular($this->module->getModuleName()),
                '{{moduleName}}'         => $this->module->getModuleName()
            ]);

            $fullFilePath = $this->getPath("resources/views/backend/" . $this->module->getModuleName()) . "/{$view}.blade.php";
            file_put_contents($fullFilePath, $viewTemplate);
            $generatedFiles[] =  $fullFilePath;
        }

        $mainMenuFile = $this->getPath("resources/views/backend/includes/")."main_menu.blade.php";
        $this->initializeFiles([
            $mainMenuFile => "Backend/Views/includes/main_menu",
        ]);

        $this->insertIntoFile(
            $mainMenuFile,
            "{{-- Main Menu --}}",
			""
		);

        return $generatedFiles;
	}

	public function getHeadings(){
		return "";
	}

	public function getModelData(){
		return "";
	}


}
