<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\Module;
use Illuminate\Support\Str;

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
                '{{displayFields}}'      => $this->getDisplayFields(),
                '{{modelNameLowercase}}' => Str::singular($this->module->getModuleName()),
                '{{modelName}}'          => $this->module->getModelName(),
                '{{moduleName}}'         => $this->module->getModuleName(),
                '{{form}}'               => $this->formGenerate($view)
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
        // $generatedFiles = array_merge($generatedFiles, $this->formGenerateCreate());
        return $generatedFiles;
    }

    public function getHeadings()
    {
        $columns = $this->module->getDisplayColumns();
        $headings = "";
        foreach ($columns as $column) {
            $headings .= "<th> 
                    <a href=\"{{ route('backend." . $this->module->getModuleName() . ".index') }}?sort=" . $column->getColumn() . "&sort_dir={{ request()->input('sort_dir')=='asc' ? 'desc' : 'asc' }}\">" . $column->getDisplay() . " 
                        @if(request()->input('sort')=='" . $column->getColumn() . "')
                            {!! request()->input('sort_dir')=='asc' ? '<i class=\"fas fa-arrow-down\"></i>' : '<i class=\"fas fa-arrow-up\"></i>' !!}
                        @endif
                    </a>
                </th>";
        }
        return $headings;
    }

    public function getDisplayFields()
    {
        $columns = $this->module->getDisplayColumns();
        $data = "";
        foreach ($columns as $column) {
            $data .= "<td> {{ $". $this->module->getModelNameLowercase()."->".$column->getColumn() . " }}</td>".PHP_EOL;
        }
        return $data;
    }

    public function formGenerate($page="create")
    {
        $formTemplate = '';

        if(in_array($page, ['create', 'edit'])){
            $viewTemplate = '';
            foreach ($this->module->getColumns() as $type) {
                $viewTemplate .= $this->buildTemplate('backend/views/formelements/'.$page.'/'.$type->getFormType(), [
                    '{{key}}'                   => $type->getColumn(),
                    '{{label}}'                 => $type->getDisplay(),
                    '{{options}}'               => $type->getFormOptions(),
                    '{{parentModule}}'          => $type->getParentModule(),
                    '{{parentModuleSinglular}}' => $type->getParentModelLowercase(),
                    '{{modelNameLowercase}}'    => $this->module->getModelNameLowercase(),
                    '{{modulename}}'            => $this->module->getModuleName(),
                ]);
            }
    
            $formTemplate = $this->buildTemplate('backend/views/formelements/'.$page.'/_form',[
                '{{modulename}}'            => $this->module->getModuleName(),
                '{{modelNameLowercase}}'    => $this->module->getModelNameLowercase(),
                '{{createElements}}'        => $viewTemplate,
            ]);
        }

        return $formTemplate;
        // $formFilePath = $this->getPath("resources/views/backend/".$this->module->getModuleName())."/_form.blade.php";
        // file_put_contents($formFilePath, $formTemplate);

        // $generatedFiles[] = $formFilePath;
        // return $generatedFiles;
    }
}
