<?php

declare(strict_types = 1);

require_once __DIR__.DIRECTORY_SEPARATOR.'vendor'.DIRECTORY_SEPARATOR.'autoload.php';
defined(constant_name: 'ROOT_DIR') || define(constant_name: 'ROOT_DIR', value: __DIR__ );

$host    = 'db';
$db      = 'deploy_monitor';
$user    = 'root';
$pass    = 'root';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$opt = [
    \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
    \PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new \PDO(dsn: $dsn, username: $user, password: $pass, options: $opt);
} catch (\PDOException $e) {
    throw new \PDOException(message: $e->getMessage(), code: (int)$e->getCode());
}

$now      = \Carbon\Carbon::now();
$command  = 'mysqldump --column-statistics=0 -u%s -p%s -P%s -h%s --triggers --routines --single-transaction %s > %s';

$now  = \Carbon\Carbon::now();
$path = implode(
    separator: DIRECTORY_SEPARATOR,
    array    : [
                   \ROOT_DIR,
                   'backups',
                   $now->year,
                   str_pad(string: '' . $now->month, length: 2, pad_string: '0', pad_type: STR_PAD_LEFT),
                   str_pad(string: '' . $now->day, length: 2, pad_string: '0', pad_type: STR_PAD_LEFT),
                   ''
               ],
);
if (false === \is_dir(filename: $path)) {
    mkdir(directory: $path, permissions: 0777, recursive: true);
}
$path    = $path . 'backup_' . date(format: 'Y-m-d_H-i-s') . '.sql';
$command = sprintf($command, 'root', 'root', 3306, 'db', 'deploy_monitor', $path);

exec(command: $command);

