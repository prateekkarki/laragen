<?php
namespace Prateekkarki\Laragen\Generators\Common;

use Prateekkarki\Laragen\Models\LaragenOptions;
use Prateekkarki\Laragen\Generators\BaseGenerator;
use Prateekkarki\Laragen\Generators\GeneratorInterface;

class Route extends BaseGenerator implements GeneratorInterface
{
    protected static $initializeFlag = 0;

    private static $destination = "routes";

    public function generate()
    {
        $generatedFiles = [];

        $backendAuthRouteFile = $this->getPath(self::$destination."/backend//")."auth.php";
        $webRouteFile = $this->getPath(self::$destination."/frontend//")."web.php";
        $backendApiRouteFile = $this->getPath(self::$destination."/backend//")."api.php";
        $backendWebRouteFile = $this->getPath(self::$destination."/backend//")."web.php";

        if (self::$initializeFlag++ == 0) {
            $this->initializeFiles([
                $backendAuthRouteFile => "backend/routes/auth",
                $backendApiRouteFile => "backend/routes/api",
                $backendWebRouteFile => "backend/routes/web"
            ]);
        }

        $laragen = LaragenOptions::getInstance();
        if ($laragen->generatorExists('Frontend\\Controller')) {
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

        if ($laragen->generatorExists('Backend\\Controller')) {
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

        if ($laragen->generatorExists('Backend\\Api')) {
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
