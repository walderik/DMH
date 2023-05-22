<?php
include_once '../header.php';

if (!isset( $_POST['groupId'])) {
    header('Location: ../index.php');
    exit;
}

$larp_group = LARP_Group::loadByIds($_POST['groupId'], $current_larp->Id);
if (empty($larp_group)) {
    header('Location: ../index.php');
    exit;
}


if ($larp_group->UserMayEdit == 0) {
    $larp_group->UserMayEdit = 1;

}
else {
    $larp_group->UserMayEdit = 0;
}

$larp_group->update();



if (isset($_POST['Referer']) && $_POST['Referer']!="") {
    header('Location: ' . $_POST['Referer']);
    exit;
}


header('Location: ../groups.php');
