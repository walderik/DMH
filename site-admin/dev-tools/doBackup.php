<?php
global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

require 'backup.php';


//Ifthe user isnt admin it may not see these pages
if (!AccessControl::hasAccessOther($current_user->Id, AccessControl::ADMIN)) {
    header('Location: ../../participant/index.php');
    exit;
}

Backup::doBackup();