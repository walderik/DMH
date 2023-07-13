<?php
include_once 'header.php';

// get the parameters from URL
$roleId = $_REQUEST["roleId"];
$value = $_REQUEST["value"];
$larpId = $_REQUEST["larpId"];


if (empty($roleId) || empty($value) || empty($larpId)) {
    echo "Värde saknas";
    return;
}

$larp_role = LARP_Role::loadByIds($roleId, $larpId);
$larp_role->StartingMoney = $value;
$larp_role->update();

echo "Sparat $value";
