<?php
include_once 'header.php';

// get the parameters from URL
if (isset($_REQUEST["roleId"])) $roleId = $_REQUEST["roleId"];
if (isset($_REQUEST["groupId"])) $groupId = $_REQUEST["groupId"];
$value = $_REQUEST["value"];
$larpId = $_REQUEST["larpId"];
$isEnd = false;
if (isset($_REQUEST["isEnd"])) $isEnd = true;


if ((empty($roleId) && empty($groupId))|| !isset($value) || empty($larpId)) {
    echo "Värde saknas";
    return;
}

if (isset($roleId)) {
    $larp_role = LARP_Role::loadByIds($roleId, $larpId);
    if ($isEnd) $larp_role->EndingMoney = $value;
    else $larp_role->StartingMoney = $value;
    $larp_role->update();
} elseif (isset($groupId)) {
    $larp_group = LARP_Group::loadByIds($groupId, $larpId);
    if ($isEnd) $larp_group->EndingMoney = $value;
    else $larp_group->StartingMoney = $value;
    $larp_group->update();
}


