<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
date_default_timezone_set('Africa/Cairo');
$host = "localhost";
$user = "root";
$password = "Abdullah123123";
$dbname = "pos_system";

$conn = mysqli_connect($host, $user, $password, $dbname);

if (!$conn) {
    die("فشل الاتصال بقاعدة البيانات: " . mysqli_connect_error());
}


if (!function_exists('app_log')) {
    function app_log($message)
    {
        $logFile = __DIR__ . '/app.log';
        $date = date('Y-m-d H:i:s');
        $msg = "[$date] $message\n";
        file_put_contents($logFile, $msg, FILE_APPEND | LOCK_EX);
    }
}
