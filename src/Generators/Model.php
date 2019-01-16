<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;

class Model extends BaseGenerator implements GeneratorInterface
{
    public function generate()
    {
        $modelTemplate = $this->buildTemplate('Model', [
            '{{modelName}}'       => $this->module->getModelName(),
            '{{massAssignables}}' => $this->getMassAssignables(),
            '{{foreignMethods}}'  => $this->getForeignMethods()
        ]);

        file_put_contents(base_path("app/Models/" . $this->module->getModelName() . ".php"), $modelTemplate);
    }

    protected function getMassAssignables()
    {
        $massAssignables = [];

        foreach ($this->module->getData() as $column => $optionString) {
            $optionArray = explode(':', $optionString);
            if (in_array($optionArray[0], ['string', 'int', 'text', 'bool', 'date'])) {
                $massAssignables[] = "'" . $column . "'";
            }
        }

        return implode(', ', $massAssignables);
    }

    protected function getForeignMethods()
    {
        $foreignMethods = "";

        foreach ($this->module->getData() as $column => $optionString) {
            $optionArray = explode(':', $optionString);
            if ($optionArray[0] == 'parent') {
                $foreignMethods .= $this->buildTemplate('Model-parent', [
                    '{{parent}}'      => str_singular($optionArray[1]),
                    '{{parentModel}}' => ucfirst(camel_case(str_singular($optionArray[1]))),
                ]);
            }
        }

        return $foreignMethods;
    }
}
