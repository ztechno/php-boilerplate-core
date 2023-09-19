<?php

// remove symlink
if(file_exists('public/theme'))
{
    exec('rm -rf ' . '"'.getcwd() . '/public/theme"');
    mkdir('public/theme');
}

if(!file_exists('public/theme'))
{
    mkdir('public/theme');
}

// publishing module assets
if(app('theme'))
{
    $dir = 'themes/'.app('theme').'/assets';
    $folders = scandir($dir);

    $res = '"'.getcwd() . '/'.$dir.'"';
    $dst = '"'.getcwd() . '/public/theme"';
    $cmd = 'ln -s '.$res.' '.$dst;
    exec($cmd);
    echo "Exec symlink ".$cmd."\n";
}
die();