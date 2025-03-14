<?php
global $root, $current_person;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

require 'backup.php';


//Ifthe user isnt admin it may not see these pages
if (!AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) {
    header('Location: ../../participant/index.php');
    exit;
}

if (isset($_GET['alt'])) {
    $alt = $_GET['alt'];
    if ($alt == 1 || $alt == 2 || $alt==3) {
        $num1 = $_GET['num1'];
        $num2 = $_GET['num2'];
        Backup::doParitialBackupImages($alt, $num1, $num2);
    } elseif ($alt  == 4) {
        Backup::doParitialBackupAttachements();
    } else {
        Backup::doParitialBackupRest();
    }
}
 Backup::doBackup();