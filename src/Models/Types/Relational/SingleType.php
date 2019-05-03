<?php
namespace Prateekkarki\Laragen\Models\Types\Relational;
use Prateekkarki\Laragen\Models\Types\RelationalType;

class SingleType extends RelationalType
{
    protected $dataType = 'integer';    
    protected $hasSingleRelation = true;
    protected $size = false;
    protected $relationalType = false;
    
    protected $stubs = [
        'modelMethod' => 'common/Models/fragments/hasOne',
        'foreignMethod' => 'common/Models/fragments/belongsTo'
    ];
}
