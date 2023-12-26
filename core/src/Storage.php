<?php

namespace Core;

class Storage
{
    static function upload($file)
    {
        $parent_path = Utility::parentPath();
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = strtotime('now').'.'.$ext;
        $dest = $parent_path . 'storage/media/'.$filename;
        copy($file['tmp_name'], $dest);

        return 'storage/' . $filename;
    }
}