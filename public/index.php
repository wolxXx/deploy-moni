<?php

require_once __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php';
chdir(directory: dirname(path: __DIR__));
defined(constant_name: 'ROOT_DIR') || define(constant_name: 'ROOT_DIR', value: __DIR__ . DIRECTORY_SEPARATOR . '..');
#ini_set('opcache.enable', 0);
ini_set(option: 'opcache.max_file_size', value: 1000);
ini_set(option: 'opcache.file_cache_only', value: 1);
ini_set(option: 'opcache.file_cache', value: __DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . '.opcache');
date_default_timezone_set("Europe/Berlin");

\Application\Manager::Factory();