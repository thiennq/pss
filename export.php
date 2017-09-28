<?php

include_once(dirname(__FILE__) . '/vendor/autoload.php');
$dotenv = new Dotenv\Dotenv(dirname(__FILE__));
$dotenv->load();

use Ifsnop\Mysqldump as IMysqldump;

$database = getenv('DATABASE') ? getenv('DATABASE') : 'balohanghieu';
$username = getenv('USER_MYSQL') ? getenv('USER_MYSQL') : 'root';
$password = getenv('PASSWORD') ? getenv('PASSWORD') : 'a';
$prefix = getenv('PREFIX') ? getenv('PREFIX') : '';

try {
    $dump = new IMysqldump\Mysqldump("mysql:host=localhost;dbname=$database", "$username", "$password");
    $file_name = 'db ' . date('Y-m-d H:i:s') . '.sql';
    $dump->start($file_name);
} catch (\Exception $e) {
    echo 'mysqldump-php error: ' . $e->getMessage();
}

?>
