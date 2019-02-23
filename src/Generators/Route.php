<?php

namespace Prateekkarki\Laragen\Generators;

use Prateekkarki\Laragen\Models\Module;

class Route extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;

    public function generate()
    {
        $generatedFiles = [];

        $routeProviderFile = (self::$initializeFlag == 0) ? $this->initializeFile($this->getPath("app/Providers/")."LaragenRouteServiceProvider.php", "RouteServiceProvider") : $this->getPath("app/Providers/")."LaragenRouteServiceProvider.php";

        $this->insertIntoFile(
            $routeProviderFile,
            str_replace("\r", '', $this->getStub('fragments/RouteMap')),
            "\n".$this->getStub('fragments/RouteMapCode')
        );

        $webAuthRouteFile = (self::$initializeFlag == 0) ? $this->initializeFile($this->getPath("routes/backend/")."auth.php", "Routes/Backend-auth") :  $this->getPath("routes/backend/")."auth.php";

        $webRouteFile = (self::$initializeFlag == 0) ? $this->initializeFile($this->getPath("routes/frontend/")."web.php", "Route") :  $this->getPath("routes/frontend/")."web.php";
        
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
		
        $webRouteFile = (self::$initializeFlag == 0) ? $this->initializeFile($this->getPath("routes/backend/")."web.php", "Routes/Backend-web") :  $this->getPath("routes/backend/")."web.php";

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
        
        self::$initializeFlag;
        return $generatedFiles;         
    }
}
