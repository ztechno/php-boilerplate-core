<?php

namespace Core;

class TableField 
{
    private $tableName;
    private $fields;
    private $module;
    
    function __construct($tableName, $fields, $module)
    {
        $this->tableName = $tableName;
        $this->fields = $fields;
        $this->module = $module;
    }

    function getFields()
    {
        $fields = $this->fields;
        $hookFile = Utility::parentPath() . "modules/$this->module/hooks/index-fields-$this->tableName.php";
        if(file_exists($hookFile))
        {
            return require $hookFile;
        }

        return $fields;
    }

    function getModule()
    {
        return $this->module;
    }

    function getTableName()
    {
        return $this->tableName;
    }
}