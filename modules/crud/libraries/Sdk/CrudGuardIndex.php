<?php

namespace Modules\Crud\Libraries\Sdk;

class CrudGuardIndex
{
    private static $guardRoute = [];
    static function set($module, $route, $callback)
    {
        if(!isset(self::$guardRoute[$module]))
        {
            self::$guardRoute[$module] = [];
        }
        self::$guardRoute[$module][$route] = $callback;
    }

    static function get()
    {
        return self::$guardRoute;
    }
}