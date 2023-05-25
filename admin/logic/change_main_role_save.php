<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['PersonId']) && isset($_POST['RoleId'])) {
        $PersonId = $_POST['PersonId'];
        $RoleId = $_POST['RoleId'];
        
        $person=Person::loadById($PersonId);
        $roles = $person->getRolesAtLarp($current_larp);
        foreach ($roles as $role)  {
            $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
            if ($role->Id == $RoleId) {
                $larp_role->IsMainRole = 1;
            }
            else {
                $larp_role->IsMainRole = 0;
            }
            $larp_role->update();
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

