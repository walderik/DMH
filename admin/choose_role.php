<?php
include_once 'header.php';

global $purpose;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } elseif (isset($_POST['Id'])) {
        $id = $_POST['Id'];
    }
    if (isset($_POST['intrigueTypeFilter'])) {
        $intrigue = Intrigue::loadById($id);
        $intrigueTypes = $intrigue->getSelectedIntrigueTypeIds();
    }
    
}
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    $operation = $_GET['operation'];
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
    } elseif (isset($_GET['Id'])) {
        $id = $_GET['Id'];
    }
    if (isset($_GET['intrigueTypeFilter'])) {
        $intrigue = Intrigue::loadById($id);
        $intrigueTypes = $intrigue->getSelectedIntrigueTypeIds();
    }
    
}
if (isset($_GET['notRegistered'])) {
    $mainroles = Role::getAllUnregisteredRoles($current_larp);
    $nonmainroles = array();
}
else {
    $mainroles = Role::getAllMainRolesNoMyslajvare($current_larp);
    $nonmainroles = Role::getAllNotMainRolesNoMyslavare($current_larp);
}


$multiple=false;
$showAbilityChoice = false;

if ($operation == "set_prop_owner_role") {
    
    $purpose = "Sätt som ägare av rekvisita";
    $url = "prop_owner_form.php";
}
elseif ($operation == "add_titledeed_owner_role") {
    $purpose = "Sätt som ägare av verksamhet";
    $url = "logic/titledeed_form_save.php";
    $multiple=true;
} elseif ($operation == "add_intrigue_actor_role") {
    $purpose = "Lägg till aktör i intrig";
    $url = "logic/view_intrigue_logic.php";
    $multiple=true;
} elseif ($operation == "exhange_intrigue_actor_role") {
    $purpose = "Byt aktör på intrig";
    $url = "logic/view_intrigue_logic.php";
} elseif ($operation == "add_concerns_role") {
    $purpose = "Lägg till karaktär(er) som ryktet handlar om";
    $url = "rumour_form.php";
    $multiple=true;
} elseif ($operation == "add_knows_role") {
    $purpose = "Lägg till karaktär(er) som känner till ryktet";
    $url = "rumour_form.php";
    $multiple=true;
} elseif ($operation == "add_has_role") {
    $purpose = "Lägg till karaktär(er) som ska ha synen";
    $url = "vision_form.php";
    $showAbilityChoice = true;
    $multiple=true;
} elseif ($operation == "add_has_role_admin") {
    $purpose = "Lägg till karaktär(er) som ska ha synen";
    $url = "vision_admin.php";
    $showAbilityChoice = true;
    $multiple=true;
} elseif ($operation == "add_magician") {
    $purpose = "Lägg till karaktärer som magiker";
    $url = "logic/view_magician_logic.php";
    $mainroles = filterAllWithAbilities($mainroles);
    $nonmainroles= null;
    $showAbilityChoice = true;
    $multiple=true;
} elseif ($operation == "add_alchemy_supplier") {
    $purpose = "Lägg till karaktärer som löjverist";
    $url = "logic/view_alchemy_supplier_logic.php";
    $mainroles = filterAllWithAbilities($mainroles);
    $nonmainroles= null;
    $showAbilityChoice = true;
    $multiple=true;
} elseif ($operation == "add_alchemist") {
    $purpose = "Lägg till karaktärer som alkemist";
    $url = "logic/view_alchemist_logic.php";
    $mainroles = filterAllWithAbilities($mainroles);
    $nonmainroles= null;
    $showAbilityChoice = true;
    $multiple=true;
} elseif ($operation == "add_subdivision_member") {
    $purpose = "Lägg till karaktärer i gruppering";
    $url = "subdivision_form.php";
    $multiple=true;
}

if ($multiple) {
    $type = "checkbox";
    $array="[]";
    
} else {
    $type="radio";
    $array="";
}
    

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


function filterAllWithAbilities($roleArray) {
    global $current_larp;
    
    $magicianRoleIds = Magic_Magician::RoleIdsByCampaign($current_larp);
    $alchemistRoleIds = Alchemy_Alchemist::RoleIdsByCampaign($current_larp);
    $alchemysupplierRoleIds = Alchemy_Supplier::RoleIdsByCampaign($current_larp);
    
    $filtereredRoles = array();
    
    foreach ($roleArray as $role) {
        if (in_array($role->Id, $magicianRoleIds)) continue;
        if (in_array($role->Id, $alchemistRoleIds)) continue;
        if (in_array($role->Id, $alchemysupplierRoleIds)) continue;
        $filtereredRoles[] = $role;
    }
    
    return $filtereredRoles;
}


include 'navigation.php';
?>
<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/show_hide_rows.js"></script>
<script src="../javascript/table_sort.js"></script>


    <div class="content"> 
      
        <h1><?php echo $purpose;?></h1>
        
     		<?php 

    		if (empty($mainroles) && empty($nonmainroles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    ?>
    		    <?php 
    		    if (!empty($intrigueTypes)) {
    		        echo "Karaktärer filtrerade på intrigtyper ". commaStringFromArrayObject($intrigue->getIntrigueTypes())."<br>";
    		        echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
    		    }
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">

    		    <?php 
    		    if (isset($id)) {
    		        echo "<input type='hidden' id='id' name='id' value='$id'>";
    		        echo "<input type='hidden' id='Id' name='Id' value='$id'>";
    		    }    
    		    if (isset($_POST['2ndReferer'])) {
    		        echo "<input type='hidden' id='2ndReferer' name='2ndReferer' value='".$_POST['2ndReferer']."'>";
    		    }
    		    if (isset($_POST['ReturnTo'])) {
    		        echo "<input type='hidden' id='ReturnTo' name='ReturnTo' value='".$_POST['ReturnTo']."'>";
    		    }
    		    ?> 
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
        		<h2>Huvudkaraktärer</h2>
    		    <?php
    		    $tableId = "main_roles";
    		    $colnum = 1;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr>".
    		          "<th></th>".
    		          "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
    		          "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Yrke</th>".
    		          "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Ålder</th>";
    		    if (IntrigueType::isInUse($current_larp)) {   		    
    		          echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Intrigtyper</th>";
    		    }
    		    if ($showAbilityChoice) {
    		        echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Kunskaps-<br>önskemål</th>";
    		    }
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>".
    	          "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Spelas av</th>".
    	          "</tr>";
 
    		    foreach ($mainroles as $role)  {
    		        $person = $role->getPerson();
    		        $show = true;
    		        if (!empty($intrigueTypes)) {
    		            $role_intrigueTypeIds = $role->getSelectedIntrigueTypeIds();
    		            if (empty(array_intersect($intrigueTypes, $role_intrigueTypeIds))) {
    		                $show = false;
    		            }
    		        }
    		        if ($show) echo "<tr>\n";
    		        else echo "<tr class='show_hide hidden'>\n";
    		        echo "<td><input id ='Role$role->Id' type='$type' name='RoleId$array' value='$role->Id'></td>";
    		        echo "<td>$role->Name</td>\n";
    		        echo "<td>" . $role->Profession . "</td>\n";
    		        echo "<td>";
    		        if (!is_null($person)) echo $person->getAgeAtLarp($current_larp);
    		        echo "</td>\n";
    		        if (IntrigueType::isInUse($current_larp)) {
    		          echo "<td>".commaStringFromArrayObject($role->getIntrigueTypes())."</td>";
    		        }
    		        if ($showAbilityChoice) {
    		            echo "<td>".commaStringFromArrayObject($role->getAbilities())."</td>";
    		        }
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>$group->Name</td>\n";
    		        }

                    echo "<td>";
                    if (!is_null($person)) echo $person->Name;
                    else echo "NPC";
                    echo "</td>";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
    		<input type="submit" value="<?php echo $purpose;?>">
    		
    		
    		<?php if (!empty($nonmainroles)) {?>
    		<h2>Sidokaraktärer</h2>
    		    <?php
    		    $tableId = "other_roles";
    		    $colnum = 1;
    		    echo "<table id='$tableId' class='data'>";
    		    echo "<tr>".
        		    "<th></th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Yrke</th>";
    		    if (IntrigueType::isInUse($current_larp)) {
    		        echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Intrigtyper</th>";
    		    }
    		    echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>".
        		    "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Spelas av</th>".
        		    "</tr>";
    		    
    		foreach ($nonmainroles as $role)  {
    		    $show = true;
    		    if (!empty($intrigueTypes)) {
    		        $role_intrigueTypeIds = $role->getSelectedIntrigueTypeIds();
    		        if (empty(array_intersect($intrigueTypes, $role_intrigueTypeIds))) {
    		            $show = false;
    		        }
    		    }
    		    if ($show) echo "<tr>\n";
    		    else echo "<tr class='show_hide hidden'>\n";
    		    echo "<td><input id ='Role$role->Id' type='$type' name='RoleId$array' value='$role->Id'></td>";
    		    echo "<td>$role->Name</td>\n";
    		    echo "<td>" . $role->Profession . "</td>\n";
    		    if (IntrigueType::isInUse($current_larp)) { 		        
    		      echo "<td>".commaStringFromArrayObject($role->getIntrigueTypes())."</td>";
    		    }
    		    $group = $role->getGroup();
    		    if (is_null($group)) {
    		        echo "<td>&nbsp;</td>\n";
    		    } else {
    		        echo "<td>$group->Name</td>\n";
    		    }
    		    $person = $role->getPerson();
    		    echo "<td>";
    		    if (!is_null($person)) echo $person->Name;
    		    else echo "NPC";
    		    echo "</td>";
    		    
    		    echo "</tr>\n";
    		}
    		echo "</table>";

            ?>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>">
			</form>
        
        <?php } ?>
        
	</div>


</body>
</html>
