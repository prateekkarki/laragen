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

        $backendAuthRouteFile = $this->getPath("routes/backend/")."auth.php";
        $webRouteFile = $this->getPath("routes/frontend/")."web.php";
        $backendApiRouteFile = $this->getPath("routes/backend/")."api.php";
        $backendWebRouteFile = $this->getPath("routes/backend/")."web.php";

        if (self::$initializeFlag++ == 0) {
            $this->initializeFiles([
                $webRouteFile => "Route",
                $backendAuthRouteFile => "backend/routes/auth",
                $backendApiRouteFile => "backend/routes/api",
                $backendWebRouteFile => "backend/routes/web"
            ]);
        }

        if(app('laragen')->generatorExists('Frontend\\Controller')){
            $this->insertIntoFile(
                $webRouteFile,
                "<?php\n",
                "use App\\Http\\Controllers\\".$this->module->getModelName()."Controller;\n"
            );
    
            $this->insertIntoFile(
                $webRouteFile,
                "/"."* Insert your routes here */",
                "\n".$this->getTabs(1)."Route::resource('".$this->module->getModuleName()."', ".$this->module->getModelName()."Controller::class);"
            );
            $generatedFiles[] = $webRouteFile;
        }
        
        if(app('laragen')->generatorExists('Backend\\Controller')){
            $this->insertIntoFile(
                $backendWebRouteFile,
                "<?php\n",
                "use App\\Http\\Controllers\\Backend\\".$this->module->getModelName()."Controller;\n"
            );

            $this->insertIntoFile(
                $backendWebRouteFile,
                "/"."* Insert your routes here */",
                "\n".$this->getTabs(1)."Route::resource('".$this->module->getModuleName()."', ".$this->module->getModelName()."Controller::class);"
            );
            $generatedFiles[] = $backendWebRouteFile;
        }
        
        if(app('laragen')->generatorExists('Backend\\Api')){
            $this->insertIntoFile(
                $backendApiRouteFile,
                "<?php\n",
                "use App\\Http\\Controllers\\Backend\\Api\\".$this->module->getModelName()."Controller;\n"
            );

            $this->insertIntoFile(
                $backendApiRouteFile,
                "/"."* Insert your routes here */",
                "\n".$this->getTabs(1)."Route::resource('".$this->module->getModuleName()."', ".$this->module->getModelName()."Controller::class);"
            );
            
            $generatedFiles[] = $backendApiRouteFile;
        }
        
        return $generatedFiles;         
    }
}
