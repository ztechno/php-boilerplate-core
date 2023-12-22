<?php

namespace Core;

class Storage
{
    static function upload($file)
    {
        $parent_path = Utility::parentPath();
        $ext = end(explode('.',$file['name']));
        $filename = strtotime('now').'.'.$ext;
        $dest = $parent_path . 'storage/media/'.$filename;
        copy($file['tmp_name'], $dest);

        return $dest;
    }
}