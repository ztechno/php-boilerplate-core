<?php

use Modules\Crud\Libraries\Sdk\CrudGuardIndex;

$auth = auth();
$publicRoutes = \Core\Request::getPublicRoutes();

if(empty($auth) && !in_array($route, $publicRoutes))
{
    header("location: ".routeTo('auth/login'));
    die();
}

if($auth && $route == 'auth/login')
{
    $AUTH_AFTER_LOGIN_SUCCESS = env('APP_PATH', '') .'/'. env('AUTH_AFTER_LOGIN_SUCCESS','');
    header("location: ". $AUTH_AFTER_LOGIN_SUCCESS);
    die();
}

$authRoute = [
    'auth/login',
    'auth/logout',
];

if(!in_array($route, $authRoute) && $auth && !is_allowed($route, $auth->id))
{
    die('Error 403. Unauthorized');
}

foreach(CrudGuardIndex::get() as $module => $guardRoute)
{
    foreach($guardRoute as $r => $callback)
    {
        if($route == $r)
        {
            $callback();
        }
    }
}