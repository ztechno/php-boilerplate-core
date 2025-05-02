<?php

use Core\Database;

$db = new Database;
$db->update('users', [
    'password' => md5($_POST['password'])
], [
    'id' => auth()->id
]);

set_flash_msg(['success'=>"Password berhasil di update"]);

header('location:'.routeTo('default/profile'));
die();