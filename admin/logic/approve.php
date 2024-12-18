<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['GroupId'])) {
        $groupId = $_POST['GroupId'];
        $group = Group::loadById($groupId);
        if (isset($group)) {
            $group->approve($current_larp, $current_person);
            header('Location: ../approval.php');
            exit;
        }
    } elseif($_POST['RoleId']) {
        $roleId = $_POST['RoleId'];
        $role = Role::loadById($roleId);
        if (isset($role)) {
            $role->approve($current_larp, $current_person);
            header('Location: ../approval.php');
            exit;
            
        }
    }
    
}
header('Location: ../index.php');
exit;


