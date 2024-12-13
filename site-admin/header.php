<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';


//Ifthe user isnt admin it may not see these pages
if (!AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) {
    header('Location: ../participant/index.php');
    exit;
}

 