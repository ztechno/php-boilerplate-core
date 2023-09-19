<?php

if(file_exists('public/theme'))
{
    exec('rm -rf ' . '"'.getcwd() . '/public/storage"');
    mkdir('public/storage');
}

if(!file_exists('public/storage'))
{
    mkdir('public/storage');
}

// publishing module assets
$res = '"'.getcwd() . '/storage/media"';
$dst = '"'.getcwd() . '/public/storage"';
$cmd = 'ln -s '.$res.' '.$dst;
exec($cmd);
echo "Exec symlink ".$cmd."\n";
die();