<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $groupId = $_POST['GroupId'];
    $group = Group::loadById($groupId);
    if (isset($group)) {
        $group->IsApproved = 1;
        $group->update();

        BerghemMailer::send_group_approval_mail($group, $current_larp);
        header('Location: ../approval.php');
        exit;
    }
    
}
header('Location: ../index.php');
exit;


