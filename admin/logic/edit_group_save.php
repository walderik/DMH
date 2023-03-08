a
<?php

global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require $root . '/includes/init.php';

//If the user isnt admin it may not use this page
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if (isset($_POST['GroupId']) && $_POST['GroupId']!="") {
        $groupId = $_POST['GroupId'];
    
        $groupArr = $_POST;
        $groupArr += ["Id" => $groupId];
        
        $group = Group::loadById($groupId);
        $group->setValuesByArray($groupArr);
        $group->update();
            
        $larp_group = LARP_Group::loadByIds($groupId, $current_larp->Id);
        

        $larp_group->setValuesByArray($_POST);
        $larp_group->update();
        
        $larp_group->deleteAllIntrigueTypes();
        $larp_group->saveAllIntrigueTypes($_POST);
    }
    if (isset($_POST['Referer']) && $_POST['Referer']!="") {
        header('Location: ' . $_POST['Referer']);
        exit;
    }
           
}
header('Location: ../index.php');