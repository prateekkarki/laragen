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
                '{{tabLinks}}'           => $this->getTabLinks(),
                '{{tabContents}}'        => $this->tabContents($view),
                '{{form}}'               => $this->formGenerate($view),
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

    public function getTabLinks()
    {
        $tabs = $this->module->getTabTitles();
        $data = "";
        foreach ($tabs as $key => $tab) {
            $activeClass = ($key==0) ? 'active' : '';
            $data .= '<li class="nav-item">'.PHP_EOL.$this->getTabs(3);
            $data .= '<a class="nav-link '. $activeClass .'" id="base-tab'.$key.'" data-toggle="tab" aria-controls="tab'.$key.'" href="#tab'.$key.'" aria-expanded="true">'.$tab. '</a>' . PHP_EOL . $this->getTabs(3);
            $data .= '</li>' . PHP_EOL . $this->getTabs(3);
        }
        return $data;
    }

    public function getDisplayFields()
    {
        $columns = $this->module->getDisplayColumns();
        $data = "";
        foreach ($columns as $column) {
            $data .= "<td> {{ $" . $this->module->getModelNameLowercase() . "->" . $column->getColumn() . " }}</td>" . PHP_EOL;
        }
        return $data;
    }

    public function tabContents($page = "create")
    {

        $tabs = $this->module->getTabs();
        $viewTemplate = "";
        foreach ($tabs as $key => $tab) {
            if ($tab =="Seo") {
                continue;
            }
            // $viewTemplate = '';
            if (in_array($page, ['create', 'edit'])) {
                foreach ($this->module->getFilteredColumns($tab) as $type) {
                    $activeClass = ($key == 0) ? 'active' : '';
                    $viewTemplate .= '<div class="tab-content px-1 pt-1">
					<div role="tabpanel" class="tab-pane '.$activeClass.'" id="tab'.$key.'" aria-expanded="true" aria-labelledby="base-tab'.$key.'">

						<div class="row mt-4 mb-4">
							<div class="col">';
                    $displayColumn = $type->getRelatedModule() == 'users' ? 'name' : 'title';
                    if (($type->hasPivot() || $type->isParent()) && $type->getRelatedModule() != 'users') {
                        $module = new Module($type->getRelatedModule(), config('laragen.modules.' . $type->getRelatedModule()));
                        $displayColumn = $module->getDisplayColumns()[0]->getColumn();
                    }
                    $viewTemplate .= $this->buildTemplate('backend/views/formelements/' . $page . '/' . $type->getFormType(), [
                        '{{key}}'                   => $type->getColumn(),
                        '{{label}}'                 => $type->getDisplay(),
                        '{{options}}'               => $type->getFormOptions(),
                        '{{relatedModule}}'         => $type->getRelatedModule(),
                        '{{relatedModelLowercase}}' => $type->getRelatedModelLowercase(),
                        '{{relatedModelDisplayColumn}}' => $displayColumn,
                        '{{modelNameLowercase}}'    => $this->module->getModelNameLowercase(),
                        '{{modulename}}'            => $this->module->getModuleName(),
                    ]);
                    $viewTemplate .= '</div></div></div></div>';
                }
            }
        }
        return $viewTemplate;
    }

    public function formGenerate($page = "create")
    {
        $viewTemplate = '';

        if (in_array($page, ['create', 'edit'])) {
            foreach ($this->module->getColumns() as $type) {
                $displayColumn = $type->getRelatedModule() == 'users' ? 'name' : 'title';
                if (($type->hasPivot() || $type->isParent()) && $type->getRelatedModule() != 'users') {
                    $module = new Module($type->getRelatedModule(), config('laragen.modules.' . $type->getRelatedModule()));
                    $displayColumn = $module->getDisplayColumns()[0]->getColumn();
                }
                $viewTemplate .= $this->buildTemplate('backend/views/formelements/' . $page . '/' . $type->getFormType(), [
                    '{{key}}'                   => $type->getColumn(),
                    '{{label}}'                 => $type->getDisplay(),
                    '{{options}}'               => $type->getFormOptions(),
                    '{{relatedModule}}'         => $type->getRelatedModule(),
                    '{{relatedModelLowercase}}' => $type->getRelatedModelLowercase(),
                    '{{relatedModelDisplayColumn}}' => $displayColumn,
                    '{{modelNameLowercase}}'    => $this->module->getModelNameLowercase(),
                    '{{modulename}}'            => $this->module->getModuleName(),
                ]);
            }
        }

        return $viewTemplate;
    }
}
