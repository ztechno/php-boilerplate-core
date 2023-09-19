<?php

use Core\Log;
use Core\Utility;
use Core\Scheduler;

$parent_path = Utility::parentPath();

if(file_exists($parent_path . 'storage/log/scheduler.txt'))
{
    die();
}

file_put_contents($parent_path . 'storage/log/scheduler.txt', strtotime('now'));

try {
    // all scheduler run here
    Scheduler::run();
} catch (\Throwable $th) {
    // throw $th;
    Log::write($th->__toString());
}

unlink($parent_path . 'storage/log/scheduler.txt');
die();