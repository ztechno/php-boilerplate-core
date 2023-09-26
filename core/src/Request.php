<?php

namespace Core;

use Core\Utility;

class Request
{
    static function getRoute()
    {
        $uri = rtrim(parse_url($_SERVER['REQUEST_URI'])['path'], '/');
        
        $request_uri = strtok($uri, '?');
        
        $route = $request_uri != '/' ? trim($request_uri,'/') : false;

        return $route;
    }

    static function process($moduleName, $file)
    {
        $parent_path = Utility::parentPath();
        $firstFile   = $parent_path . 'modules/' . $moduleName . "/process/" . $file . ".php";
        $secondFile  = $parent_path . 'modules/' . $moduleName . "/process/" . $file . "/index.php";

        $response = null;
        $fileExists = false;
        if(file_exists($firstFile))
        {
            $fileExists = true;
            $response = require $firstFile;
        }
        elseif(file_exists($secondFile))
        {
            $fileExists = true;
            $response = require $secondFile;
        }

        if(is_string($response))
        {
            echo $response;
            die();
        }
        elseif(is_array($response))
        {
            echo json_encode($response);
            die();
        }
        else
        {
            if(!$fileExists)
            {
                http_response_code(404);
                die('Error 404. file modules/'.$moduleName . "/process/" . $file . '.php Not Found.');
            }
        }

    }

    static function isMethod($method)
    {
        return strtolower($_SERVER['REQUEST_METHOD']) == strtolower($method);
    }

    static function get($key = false)
    {
        if($key)
        {
            return isset($_GET[$key]) ? $_GET['key'] : null;
        }
        
        return $_GET;
    }
    
    static function post($key = false)
    {
        if($key)
        {
            return isset($_POST[$key]) ? $_POST['key'] : null;
        }
        
        return $_GET;
    }

    static function guarding($route, $isApiRoute = false)
    {
        $guardDefaultFile = $isApiRoute ? 'api' : 'index';
        $modules = getModules();
        $parent_path = Utility::parentPath();
        foreach($modules as $module)
        {
            $guardFile = $parent_path . $module . "/guards/$guardDefaultFile.php";
            if(file_exists($guardFile))
            {
                // can access $route in required file
                require $guardFile;
            }
        }
    }

    static function tokenValidationRouteException()
    {
        $routes = [];
        $modules = getModules();
        $parent_path = Utility::parentPath();
        foreach($modules as $module)
        {
            $guardFile = $parent_path . $module . "/guards/route-token-validation-exception.php";
            if(file_exists($guardFile))
            {
                // can access $route in required file
                $routes = array_merge($routes, require $guardFile);
            }
        }

        return $routes;
    }
}