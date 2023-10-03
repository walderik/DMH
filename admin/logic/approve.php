<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['GroupId'])) {
        $groupId = $_POST['GroupId'];
        $group = Group::loadById($groupId);
        if (isset($group)) {
            $group->IsApproved = 1;
            $group->update();
    
            BerghemMailer::send_group_approval_mail($group, $current_larp);
            header('Location: ../approval.php');
            exit;
        }
    } elseif($_POST['RoleId']) {
        $roleId = $_POST['RoleId'];
        $role = Role::loadById($roleId);
        if (isset($role)) {
            $role->IsApproved = 1;
            $role->update();
            
            BerghemMailer::send_role_approval_mail($role, $current_larp);
            header('Location: ../approval.php');
            exit;
            
        }
    }
    
}
header('Location: ../index.php');
exit;


