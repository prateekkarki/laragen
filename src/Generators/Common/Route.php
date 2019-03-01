<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Route extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;

    public function generate()
    {
        $generatedFiles = [];

        $routeProviderFile = $this->getPath("app/Providers/")."LaragenRouteServiceProvider.php";
        $webAuthRouteFile = $this->getPath("routes/backend/")."auth.php";
        $webRouteFile = $this->getPath("routes/frontend/")."web.php";
        $backendWebRouteFile = $this->getPath("routes/backend/")."web.php";

        if(self::$initializeFlag++ == 0){
            $this->initializeFiles([
                $webAuthRouteFile => "Routes/Backend-auth",
                $webRouteFile => "Route",
                $backendWebRouteFile => "Routes/Backend-web"
            ]);
        }
        
        $this->initializeFiles([
            $routeProviderFile => "RouteServiceProvider",
        ]);

        $this->insertIntoFile(
            $routeProviderFile,
            $this->getStub('fragments/RouteMap'),
            "\n".$this->getStub('fragments/RouteMapCode')
        );

        $this->insertIntoFile(
            $webRouteFile,
            "<?php\n",
			"use App\\Http\\Controllers\\".$this->module->getModelName()."Controller;\n"
		);

        $this->insertIntoFile(
            $webRouteFile,
            "/" . "* Insert your routes here */",
            "\n".$this->getTabs(1)."Route::resource('".$this->module->getModuleName()."', ".$this->module->getModelName()."Controller::class);"
        );

		$generatedFiles[] = $webRouteFile;
		
        $this->insertIntoFile(
            $backendWebRouteFile,
            "<?php\n",
			"use App\\Http\\Controllers\\".$this->module->getModelName()."Controller;\n"
		);

        $this->insertIntoFile(
            $backendWebRouteFile,
            "/" . "* Insert your routes here */",
            "\n".$this->getTabs(1)."Route::resource('".$this->module->getModuleName()."', ".$this->module->getModelName()."Controller::class);"
        );
        
        $generatedFiles[] = $backendWebRouteFile;
        
        return $generatedFiles;         
    }
}
