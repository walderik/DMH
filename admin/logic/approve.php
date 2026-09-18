<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['GroupId'])) {
        $groupId = $_POST['GroupId'];
        $group = Group::loadById($groupId);
        if (isset($group)) {
            $group->approve($current_larp, $current_person);
            $redirect = $_SERVER['HTTP_REFERER'] ?? '../approval.php';  
            header('Location: ' . $redirect);
            exit;
        }
    } elseif(isset($_POST['RoleId'])) {
        $roleId = $_POST['RoleId'];
        $role = Role::loadById($roleId);
        if (isset($role)) {
            $role->approve($current_larp, $current_person);
            $redirect = $_SERVER['HTTP_REFERER'] ?? '../approval.php';
            header('Location: ' . $redirect);
            exit;
            
        }
    } elseif(isset($_POST['RumourId'])) {
        $rumourId = $_POST['RumourId'];
        $rumour = Rumour::loadById($rumourId);
        if (isset($rumour)) {
            $rumour->Approved = 1;
            $rumour->update();
            $redirect = $_SERVER['HTTP_REFERER'] ?? '../rumour_admin.php';
            header('Location: ' . $redirect);
            exit;
            
        }
    }
    
}
header('Location: ../index.php');
exit;


