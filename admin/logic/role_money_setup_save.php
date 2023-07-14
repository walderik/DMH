<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $which_roles=$_POST['which_roles'];
    $which_roles_effect=$_POST['which_roles_effect'];
    
    $wealths = Wealth::allActive($current_larp);
    $wealth_values = array();
    foreach ($wealths as $wealth) {
        $key = "wealth_".$wealth->Id;
        $wealth_values[$wealth->Id] = $_POST[$key];
    }
    
    if (isset($_POST['larp'])) $larpId = $_POST['larp'];
    $percent_min=$_POST['percent_min'];
    $percent_max=$_POST['percent_max'];
    
    $fixed_sum_min=$_POST['fixed_sum_min'];
    $fixed_sum_max=$_POST['fixed_sum_max'];

} else {
    header('Location: ../roles_money.php');
    exit;
}

if ($which_roles == "all") {
    $roles = Role::getAllRoles($current_larp);
} elseif ($which_roles == "main") {
    $roles = Role::getAllMainRoles($current_larp, false);
} elseif ($which_roles == "notmain") {
    $roles = Role::getAllNotMainRoles($current_larp, false);
}

foreach ($roles as $role) {
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    if (($which_roles_effect == "notset") && isset($larp_role->StartingMoney)) {
        continue;
    }
    
    $sum = 0;
    if (isset($role->WealthId)) $sum += $wealth_values[$role->WealthId];
    
    if (isset($larpId)) {
        $old_larp_role = LARP_Role::loadByIds($role->Id, $larpId);
        if (isset($old_larp_role)) {
            $percent = rand($percent_min, $percent_max);
            $sum += $old_larp_role->EndingMoney * $percent / 100;
        }
    }
    
    $sum += rand($fixed_sum_min, $fixed_sum_max);
       
    //Sätt inget om värdet är 0
    if ($sum == 0) continue;
    
    $larp_role->StartingMoney = $sum;
    $larp_role->update();
}



header('Location: ../roles_money.php');
