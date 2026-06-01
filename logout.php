<?php
session_start();
session_destroy();

$docRoot = str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT']);
$dirPath = str_replace('\\', '/', dirname(__DIR__));
$root = rtrim(str_replace($docRoot, '', $dirPath), '/');

header('Location: ' . $root . '/auth/login.php');
exit;
?>