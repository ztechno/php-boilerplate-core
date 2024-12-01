<?php

namespace Core;

use Core\Database;

class Route {

    static private $allowed_routes = [];

    /**
     * data = [
     *  route_path => etc
     * ]
     */
    static function additional_allowed_routes($data)
    {
        self::$allowed_routes[] = $data;
    }
    
    static function allowed_routes($user_id)
    {
        $db    = new Database();

        $query = "SELECT role_routes.route_path FROM `user_roles` JOIN roles ON roles.id = user_roles.role_id JOIN role_routes ON role_routes.role_id = user_roles.role_id WHERE user_id=$user_id";
        $db->query = $query;
        $allowed_routes = $db->exec('all');

        return array_merge($allowed_routes, self::$allowed_routes);
    }

}