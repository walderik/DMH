<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['PersonId']) && isset($_POST['RoleId'])) {
        $PersonId = $_POST['PersonId'];
        $RoleIds = $_POST['RoleId'];
        
        $person=Person::loadById($PersonId);
        $roles = $person->getRolesAtLarp($current_larp);
        foreach ($roles as $role)  {
            if (in_array($role->Id, $RoleIds)) {
                $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
                if ($larp_role->IsMainRole) continue; //Ta aldrig bort huvudkaraktärens anmälan
                LARP_Role::delete($larp_role->Id);
             }
        }
        header("Location: ../view_person.php?id=$PersonId");
        exit;
        
    }
    else {
        header('Location: ../index.php');
        exit;
    }
}

header('Location: ../roles.php?');
exit;

