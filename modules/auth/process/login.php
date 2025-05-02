<?php

use Core\Request;
use Core\Session;
use Core\Database;

$success_msg = get_flash_msg('success');
$error_msg = get_flash_msg('error');
$old = get_flash_msg('old');

if(Request::isMethod('post'))
{
    // login process here
    $db = new Database;
    $user = $db->single('users', [
        'username' => $_POST['username'],
        'password' => md5($_POST['password'])
    ]);

    if($user)
    {
        Session::set(['user_id'=>$user->id]);
        header('location:'.routeTo(env('AUTH_AFTER_LOGIN_SUCCESS', '/')));
        die();
    }
    else
    {
        set_flash_msg([
            'error'=>__('auth.message.login_fail'),
            'old'  => $_POST
        ]);
        header('location:'.routeTo('auth/login'));
        die();
    }

}

$view = env('APP_THEME') == 'sneat' ? 'auth/views/sneat-login' : 'auth/views/login';

return view($view, compact(
    'success_msg',
    'error_msg',
    'old'
));