<?php

if(file_exists('public/storage'))
{
    exec('rm -rf ' . '"'.getcwd() . '/public/storage"');
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
    {
        mkdir('public/storage');
    }
}

if(!file_exists('public/storage') && strtoupper(substr(PHP_OS, 0, 3)) === 'WIN')
{
    mkdir('public/storage');
}

// publishing module assets
$res = '"'.getcwd() . '/storage/media"';
$dst = '"'.getcwd() . '/public/storage"';
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') 
{
    $cmd = 'mklink /J '.$dst.' '.$res;
}
else
{
    $cmd = 'ln -s '.$res.' '.$dst;
}
exec($cmd);
echo "Exec symlink ".$cmd."\n";
die();