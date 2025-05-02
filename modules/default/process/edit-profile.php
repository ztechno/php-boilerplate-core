<?php

use Core\Database;
use Core\Page;
use Core\Request;

$title = __("default.label.profile");
$db = new Database;
$user = isset($_GET['user_id']) && !empty($_GET['user_id']) ? $db->single('users', ['id' => $_GET['user_id']]) : auth();
$success_msg = get_flash_msg('success');
$error_msg   = get_flash_msg('error');

if(in_array('assessment', explode(',',env('APP_MODULES'))))
{
    $user->assessment_profile = $db->single('assessment_profiles',[
        'user_id' => $user->id
    ]);
}

$user->profile = $db->single('profile', [
    'user_id' => $user->id
]);

if(Request::isMethod('POST'))
{
    $db->update('users', $_POST['users'], ['id' => $user->id]);
    if($user->profile)
    {
        $db->update('profile', $_POST['profile'], ['user_id' => $user->id]);
    }
    else
    {
        $_POST['profile']['user_id'] = $user->id;
        $_POST['profile']['name'] = $user->name;
        $db->insert('profile', $_POST['profile']);
    }
    if(in_array('assessment', explode(',',env('APP_MODULES'))))
    {
        if($user->assessment_profile)
        {
            $db->update('assessment_profiles', $_POST['assessment_profiles'], [
                'user_id' => $user->id
            ]);
        }
        else
        {
            $_POST['assessment_profiles']['user_id'] = $user->id;
            $db->insert('assessment_profiles', $_POST['assessment_profiles']);
        }
    }

    set_flash_msg(['success'=>"Profile berhasil di update"]);

    header('location:'.routeTo('default/profile'));
    die();
}

Page::setTitle($title);
Page::setActive("default.label.profile");
Page::setModuleName($title);
Page::setBreadcrumbs([
    [
        'url' => routeTo('/'),
        'title' => __('crud.label.home')
    ],
    [
        'title' => 'Profile',
        'url' => routeTo('default/profile')
    ],
    [
        'title' => 'Edit Profile'
    ]
]);



return view('default/views/edit-profile', compact('user','error_msg','success_msg'));