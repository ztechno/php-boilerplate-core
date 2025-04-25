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
        $currentRoute  = Request::getRoute();

        if(isset($fields['_userstamp']) && ($currentRoute == 'crud/index' || $currentRoute == 'api/crud/datatable'))
        {
            $fields['created_at'] = [
                'label' => __('crud.label.created_at'),
                'type' => 'text',
                'search' => $this->tableName .'.created_at'
            ];
            $fields['created_by'] = [
                'label' => __('crud.label.created_by'),
                'type' => 'options-obj:users,id,name',
                'search' => $this->tableName .'.created_by'
            ];
            $fields['updated_at'] = [
                'label' => __('crud.label.updated_at'),
                'type' => 'text',
                'search' => $this->tableName .'.updated_at'
            ];
            $fields['updated_by'] = [
                'label' => __('crud.label.updated_by'),
                'type' => 'options-obj:users,id,name',
                'search' => $this->tableName .'.updated_by'
            ];

            unset($fields['_userstamp']);
        }

        // for crud
        $actionHooks = 'index';
        if(startWith($currentRoute, 'crud/'))
        {
            $actionHooks = str_replace('crud/','', $currentRoute);
        }
        
        // will deprecated
        $hookFile = Utility::parentPath() . "modules/$this->module/hooks/$actionHooks-fields-$this->tableName.php";
        if(file_exists($hookFile))
        {
            return require $hookFile;
        }
        
        // new
        $hookFile = Utility::parentPath() . "modules/$this->module/hooks/$this->tableName/$actionHooks-fields.php";
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