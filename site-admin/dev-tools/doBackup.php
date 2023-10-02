<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

require 'backup.php';


//Ifthe user isnt admin it may not see these pages
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}

Backup::doBackup();