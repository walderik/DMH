<?php
include_once 'header.php';

// get the parameters from URL
$roleId = $_REQUEST["roleId"];
$text = str_replace("<br />", "\n", $_REQUEST["text"]);


if (empty($roleId)) {
    return;
}

$role = Role::loadById($roleId);
$role->OrganizerNotes=$text;
$role->update();

