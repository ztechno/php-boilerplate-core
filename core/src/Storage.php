<?php

namespace Core;

class Storage
{
    static function upload($file)
    {
        $ext = end(explode('.',$file['name']));
        $filename = strtotime('now').'.'.$ext;
        $dest = 'storage/media/'.$filename;
        copy($file['tmp_name'], $dest);

        return $dest;
    }
}