<?php
$time_start = microtime(true);

include_once __DIR__.'/lib/init.php';

// simple call to build the page
$page = new page;
$page->init();

print_r($page);

$time_end = microtime(true);
$time = $time_end - $time_start;

echo "did everything in $time seconds\n";