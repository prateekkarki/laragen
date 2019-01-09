<?php

namespace Prateekkarki\Laragen\Generators;
use Prateekkarki\Laragen\Models\Module;
use Prateekkarki\Laragen\Models\DataOption;

class Migration extends BaseGenerator implements GeneratorInterface
{
    protected static $counter = 0;

    public function generate(Module $module)
    {
        $migrationTemplate = $this->buildTemplate('Migration', [
            '{{modelName}}'         => $module->getModelName(),
            '{{modelNamePlural}}'   => $module->getModelNamePlural(),
            '{{moduleName}}'        => $module->getModuleName(),
            '{{modelTableSchema}}'  => $this->getSchema($module)
        ]);
        $dateSuffix = (int)date('His') + ++self::$counter;
        file_put_contents(database_path() . "/migrations/" . date('Y_m_d_') . $dateSuffix . "_create_" . $module->getModuleName() . "_table.php", $migrationTemplate);
    }

    protected function getSchema(Module $module)
    {
        $schema = "";
        $keyArray = array_keys($module->getData());
        $lastColumn = array_pop($keyArray);

        foreach ($module->getData() as $column => $optionString) {
            $option = new DataOption($column, $optionString);

            $schema .= $option->getSchema();

            if ($column != $lastColumn)
                $schema .= PHP_EOL . $this->getTabs(3);
        }

        return $schema;
    }
}
