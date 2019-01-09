<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class Model extends BaseGenerator implements GeneratorInterface
{
    public function generate(Module $module)
    {
        $migrationTemplate = $this->buildTemplate('Model', [
            '{{modelName}}'       => $module->getModelName(),
            '{{massAssignables}}' => '',
            '{{foreignMethods}}'  => ''
        ]);

        file_put_contents(base_path("app/Models/" . $module->getModelName() . ".php"), $migrationTemplate);
    }
}
