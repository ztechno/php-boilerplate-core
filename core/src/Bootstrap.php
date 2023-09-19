<?php

namespace Core;

use Core\Request;
use Core\Utility;

class Bootstrap
{
    static function init()
    {
        require Utility::parentPath() . 'core/functions.php';

        // main configuration
        ini_set('display_errors', app('debug', true));
        ini_set('session.save_path', Utility::parentPath() . app('session_path', 'storage/session') );
        date_default_timezone_set(app('timezone', 'Asia/Jakarta'));

        session_start();

        // request handler
        $module = Request::getRoute();

        if(empty($module))
        {
            $module = app('default_module') . '/index';
        }

        // explode module
        $moduleData = explode('/', $module);
        
        // 0 => module name
        // 1 to N => process file
        $moduleName = $moduleData[0];
        
        unset($moduleData[0]);
        $processFile = implode('/', $moduleData);

        Request::process($moduleName, $processFile);

    }
}