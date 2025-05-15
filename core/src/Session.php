<?php

namespace Core;

class Session
{
    static function get($key = false)
    {
        if($key)
        {
            if(isset($_SESSION[$key]))
                return $_SESSION[$key];

            if($key == 'auth' && isset($_SESSION['user_id']))
            {
                $db   = new Database;
                $user = $db->single('users',[
                    'id' => $_SESSION['user_id']
                ]);

                $user->profile = $db->single('profile', [
                    'user_id' => $user->id
                ]);

                return $user;
            }
            return;
        }

        $data = $_SESSION;
        
        return (new ArrayHelper($data))->toObject();
    }

    static function set($params)
    {
        if(!is_array($params)) return false;
        foreach($params as $key => $value)
        {
            $_SESSION[$key] = $value;
        }
    }

    static function clear($key)
    {
        unset($_SESSION[$key]);
    }

    static function destroy()
    {
        setcookie('remember_token', '', time() - 3600, "/");
        session_destroy();
    }

    static function set_flash($key, $value)
    {
        $_SESSION['flash'][$key] = $value;
    }

    static function get_flash($key = false)
    {
        if($key)
        {
            if(isset($_SESSION['flash'][$key]))
            {
                $flash = $_SESSION['flash'][$key];
                unset($_SESSION['flash'][$key]);
                return $flash;
            }

            return false;
        }

        return (new ArrayHelper($_SESSION['flash']))->toObject();
    }
}
