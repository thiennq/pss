<?php

require_once('../vendor/autoload.php');
$dotenv = new Dotenv\Dotenv(dirname(dirname(__FILE__)));
$dotenv->load();

use Ifsnop\Mysqldump as IMysqldump;

$database = getenv('DATABASE') ? getenv('DATABASE') : 'balohanghieu';
$username = getenv('USER_MYSQL') ? getenv('USER_MYSQL') : 'root';
$password = getenv('PASSWORD') ? getenv('PASSWORD') : 'a';
$prefix = getenv('PREFIX') ? getenv('PREFIX') : '';

try {
    $dump = new IMysqldump\Mysqldump("mysql:host=localhost;dbname=$database", "$username", "$password");
    $file_name = '../backup/db.sql';
    $dump->start($file_name);
} catch (\Exception $e) {
    echo 'mysqldump-php error: ' . $e->getMessage();
}

?>
