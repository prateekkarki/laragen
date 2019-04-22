<?php
namespace Prateekkarki\Laragen\Models\Types;

class ParentType extends LaragenType
{
    public function getSchema()
    {
        $schema = "";
        $parent = $this->typeOption;
        $schema .= "\$table->bigInteger('".str_singular($parent)."_id')->unsigned()->nullable();".PHP_EOL.$this->getTabs(3);
        $schema .= "\$table->foreign('".str_singular($parent)."_id')->references('id')->on('$parent')->onDelete('set null');".PHP_EOL;
        return $schema;
	}
}
