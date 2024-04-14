<?php
include_once '../header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    
    $which_groups_effect=$_POST['which_groups_effect'];
    
    $wealths = Wealth::allActive($current_larp);
    $wealth_values = array();
    foreach ($wealths as $wealth) {
        $key = "wealth_".$wealth->Id;
        $wealth_values[$wealth->Id] = $_POST[$key];
    }

    $wealth_values_per_member = array();
    foreach ($wealths as $wealth) {
        $key = "wealth_".$wealth->Id."_per_member";
        $wealth_values_per_member[$wealth->Id] = $_POST[$key];
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

$groups = Group::getAllRegistered($current_larp);

foreach ($groups as $group) {
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    if (($which_groups_effect == "notset") && isset($larp_group->StartingMoney)) {
        continue;
    }
    
    $sum = 0;
    
    $sum += $wealth_values[$group->WealthId];
    $main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);
    if (isset($main_characters_in_group)) $sum += $wealth_values_per_member[$group->WealthId] * count($main_characters_in_group);
    if (isset($larpId)) {
        $old_larp_group = LARP_Group::loadByIds($group->Id, $larpId);
        if (isset($old_larp_role)) {
            $percent = rand($percent_min, $percent_max);
            $sum += $old_larp_group->EndingMoney * $percent / 100;
        }
    }
    
    $sum += rand($fixed_sum_min, $fixed_sum_max);
       
    //Sätt inget om värdet är 0
    if ($sum == 0) continue;
    
    $larp_group->StartingMoney = $sum;
    $larp_group->update();
}



header('Location: ../groups_money.php');
