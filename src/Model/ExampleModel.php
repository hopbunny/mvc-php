<?php 

namespace Model;

class ExampleModel 
{

    use ModelTrait;

    protected static function getTableName(): string
    {
        return 'jm';
    }
    
}