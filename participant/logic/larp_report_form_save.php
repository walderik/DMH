<?php

global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $larp_role = LARP_Role::loadByIds($_POST['RoleId'], $current_larp->Id);
    if (!isset($larp_role)) {
        header('Location: ../index.php');
        exit;
    }
    $role = Role::loadById($larp_role->RoleId);

    if (Person::loadById($role->PersonId)->UserId != $current_user->Id) {
        header('Location: index.php'); //Inte din roll
        exit;
    }
    $larp_role->WhatHappened = $_POST['WhatHappened'];
    $larp_role->WhatHappendToOthers = $_POST['WhatHappendToOthers'];
    $larp_role->update();

}

header('Location: ../index.php');