<?php
namespace Prateekkarki\Laragen\Generators\Backend;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;
use Prateekkarki\Laragen\Models\Module;
use Illuminate\Support\Str;

class View extends BaseGenerator implements GeneratorInterface
{    
    protected static $initializeFlag = 0;
    protected $generatedFiles = [];

    public function generate()
    {

        $viewsToBeGenerated = ['index', 'create', 'edit'];

        foreach ($viewsToBeGenerated as $view) {
            $viewTemplate = $this->buildTemplate('backend/views/'.$view, [
                '{{headings}}' 			 => $this->getHeadings($this->module),
                '{{displayFields}}'      => $this->getDisplayFields($this->module, $this->module->getModelNameLowercase()),
                '{{modelNameLowercase}}' => Str::singular($this->module->getModuleName()),
                '{{modelName}}'          => $this->module->getModelName(),
                '{{moduleName}}'         => $this->module->getModuleName(),
                '{{tabLinks}}'           => $this->getTabLinks(),
                '{{tabContents}}'        => $this->tabContents($view),
            ]);

            $fullFilePath = $this->getPath("resources/views/backend/".$this->module->getModuleName())."/{$view}.blade.php";
            file_put_contents($fullFilePath, $viewTemplate);
            $this->generatedFiles[] = $fullFilePath;
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
        return $this->generatedFiles;
    }

    public function getHeadings($module, $simple = false)
    {
        $columns = $module->getDisplayColumns();
        $headings = "";
        foreach ($columns as $column) {
            $headings .= 
                $simple ? 
                "<th>" . $column->getDisplay() . "</th>" : 
                "<th> 
                    <a href=\"{{ route('backend." . $this->module->getModuleName() . ".index') }}?sort=" . $column->getColumn() . "&sort_dir={{ request()->input('sort_dir')=='asc' ? 'desc' : 'asc' }}\">" . $column->getDisplay() . " 
                        @if(request()->input('sort')=='" . $column->getColumn() . "')
                            {!! request()->input('sort_dir')=='asc' ? '<i class=\"fas fa-arrow-down\"></i>' : '<i class=\"fas fa-arrow-up\"></i>' !!}
                        @endif
                    </a>
                </th>";
        }
        return $headings;
    }

    public function getTabLinks()
    {
        $tabs = $this->module->getTabTitles();
        $data = "";
        foreach ($tabs as $key => $tab) {
            $activeClass = ($key==0) ? 'active' : '';
            $data .= '<li class="nav-item">'.PHP_EOL.$this->getTabs(7);
            $data .= '<a class="nav-link '. $activeClass .'" id="base-tab'.$key.'" data-toggle="tab" aria-controls="tab'.$key.'" href="#tab'.$key.'" aria-expanded="true">'.$tab. '</a>' . PHP_EOL . $this->getTabs(6);
            $data .= '</li>';
            $data .= ($tab!==last($tabs)) ? PHP_EOL . $this->getTabs(6) : '';
        }
        return $data;
    }

    public function getDisplayFields($module, $model)
    {
        $columns = $module->getDisplayColumns();
        $data = "";
        foreach ($columns as $column) {
            $data .= "<td> {{ $" . $model . "->" . $column->getColumn() . " }}</td>" . PHP_EOL;
        }
        return $data;
    }

    public function buildFormElement($page, $type)
    {
        $displayColumn = $type->getRelatedModule() == 'users' ? 'name' : 'title';
        if (($type->hasPivot() || $type->isParent()) && $type->getRelatedModule() != 'users') {
            $relatedModule = app('laragen')->getModule(Str::plural(strtolower(Str::snake($type->getRelatedModel()))));
            $displayColumn = $relatedModule->getDisplayColumns()[0]->getColumn();
        }
        $formElement = $this->buildTemplate('backend/views/formelements/' . $page . '/' . $type->getFormType(), [
            '{{key}}'                       => $type->getColumnKey(),
            '{{column}}'                    => $type->getColumn(),
            '{{label}}'                     => $type->getDisplay(),
            '{{options}}'                   => $type->getFormOptions(),
            '{{relatedModule}}'             => $type->getRelatedModule(),
            '{{relatedModelLowercase}}'     => $type->getRelatedModelLowercase(),
            '{{relatedModelDisplayColumn}}' => $displayColumn,
            '{{modelNameLowercase}}'        => $this->module->getModelNameLowercase(),
            '{{moduleName}}'                => $this->module->getModuleName(),
        ]) . PHP_EOL;
        return  "@can('edit_".$this->module->getModuleName()."_".$type->getColumnKey()."')" . PHP_EOL . $formElement . PHP_EOL . "@endcan" . PHP_EOL;
    }


    public function buildMultiple($page, $type)
    {
        $displayColumn = $type->getRelatedModule() == 'users' ? 'name' : 'title';
        $relatedModule = app('laragen')->getModule($this->module->getModelNameLowercase()."_".$type->getColumn());
        $displayColumn = $relatedModule->getDisplayColumns()[0]->getColumn();
        $formElement = $this->buildTemplate('backend/views/formelements/' . $page . '/' . $type->getFormType(), [
            '{{key}}'                       => $type->getColumn(),
            '{{label}}'                     => $type->getDisplay(),
            '{{relatedModule}}'             => $type->getRelatedModule(),
            '{{headings}}'                  => $this->getHeadings($relatedModule, true),
            '{{displayFields}}'             => $this->getDisplayFields($relatedModule, $type->getRelatedModelLowercase()),
            '{{relatedModelLowercase}}'     => $type->getRelatedModelLowercase(),
            '{{relatedModelDisplayColumn}}' => $displayColumn,
            '{{modelNameLowercase}}'        => $this->module->getModelNameLowercase(),
            '{{modulename}}'                => $this->module->getModuleName(),
        ]) . PHP_EOL;
        return $formElement;
    }

    public function tabContents($page = "create")
    {
        if (!in_array($page, ['create', 'edit'])) return "";
        $tabs = $this->module->getTabs();
        $tabTitles = $this->module->getTabTitles();
        $viewTemplate ='<div class="tab-content px-1 pt-1">'.PHP_EOL;
        foreach ($tabs as $key => $tab) {
            $activeClass = ($key == 0) ? 'active' : '';
            $viewTemplate .= $this->getTabs(6).'<div role="tabpanel" class="tab-pane '.$activeClass.'" id="tab'.$key.'" aria-expanded="true" aria-labelledby="base-tab'.$key.'">'.PHP_EOL;
            $viewTemplate .= $this->getTabs(7).'<div class="row mt-4 mb-4">'.PHP_EOL;
            $viewTemplate .= $this->getTabs(8).'<div class="col">'.PHP_EOL;


            $typeTemplate = "";
            if(is_string($tab)&&!in_array($tab, ['hasFile', 'hasImage', 'Seo'])){
                $types = $this->module->getColumnsData();
                $type = $types[Str::plural(Str::snake(strtolower($tab)))];
                $typeTemplate .= $this->buildMultiple($page, $type);
            }else{
                foreach ($this->module->getFilteredColumns($tab) as $type) {
                    $typeTemplate .= $this->buildFormElement($page, $type);
                }
            }
            
            $viewTemplate .= $this->getTabs(9)."@include('backend.".$this->module->getModuleName().".".$page.".form_parts.". strtolower(Str::title($tabTitles[$key])) ."')".PHP_EOL;
            
            $fullFilePath = $this->getPath("resources/views/backend/".$this->module->getModuleName()."/".$page."/form_parts/").strtolower(Str::title($tabTitles[$key])).".blade.php";
            file_put_contents($fullFilePath, $typeTemplate);
            $this->generatedFiles[] = $fullFilePath;

            $viewTemplate .= $this->getTabs(8).'</div>'.PHP_EOL;
            $viewTemplate .= $this->getTabs(7).'</div>'.PHP_EOL;
            $viewTemplate .= $this->getTabs(6).'</div>'.PHP_EOL;
        }
        $viewTemplate .= $this->getTabs(5).'</div>'.PHP_EOL;
        return $viewTemplate;
    }
}
