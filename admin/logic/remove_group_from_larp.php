<?php
include_once '../header.php';

if (!isset( $_POST['groupId'])) {
    header('Location: ../index.php');
    exit;
}

$groupId = $_POST['groupId'];

$larp_group = LARP_Group::loadByIds($groupId, $current_larp->Id);
if (empty($larp_group)) {
    header('Location: ../index.php');
    exit;
}
$group = Group::loadById($groupId);

//Finns det medlemmar i gruppen?
$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);
if (!empty($main_characters_in_group)) exit;
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($group, $current_larp);
if (!empty($non_main_characters_in_group)) exit;

//Kopplad till intrig
$intrigue = Intrigue::getAllIntriguesForGroup($groupId, $current_larp->Id);
if (!empty($intrigue)) exit;

LARP_Group::delete($larp_group->Id);

header('Location: ../groups.php');
