<?php

// remove symlink
if(file_exists('public/assets'))
{
    exec('rm -rf ' . '"'.getcwd() . '/public/assets"');
    mkdir('public/assets');
}

if(!file_exists('public/assets'))
{
    mkdir('public/assets');
}

// publishing module assets
$dir = 'modules';
$folders = scandir($dir);

foreach($folders as $folder)
{
    if (!in_array($folder,array(".","..")))
    {
        $res = '"'.getcwd() . '/'.$dir.'/'.$folder.'/assets"';
        $dst = '"'.getcwd() . '/public/assets/'.$folder.'"';
        $cmd = 'ln -s '.$res.' '.$dst;
        exec($cmd);
        echo "Exec symlink ".$cmd."\n";
    }
}
die();