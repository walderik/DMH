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



$multiple=false;

if ($operation == "set_prop_owner_role") {
    
    $purpose = "Sätt som ägare av rekvisita";
    $url = "prop_owner_form.php";
}
elseif ($operation == "add_titledeed_owner_role") {
    $purpose = "Sätt som ägare av lagfart";
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
} elseif ($operation == "add_magician") {
    $purpose = "Lägg till karaktärer som magiker";
    $url = "logic/view_magician_logic.php";
    $multiple=true;
} elseif ($operation == "add_alchemy_supplier") {
    $purpose = "Lägg till karaktärer som löjverist";
    $url = "logic/view_alchemy_supplier_logic.php";
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
    		$mainroles = Role::getAllMainRolesNoMyslajvare($current_larp);
    		$nonmainroles = Role::getAllNotMainRolesNoMyslavare($current_larp);
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
    		          "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Yrke</th>";
    		    if (IntrigueType::isInUse($current_larp)) {   		    
    		          echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Intrigtyper</th>";
    		    }
    	          echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>".
    	          "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Spelas av</th>".
    	          "</tr>";
 
    		    foreach ($mainroles as $role)  {
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
                    echo "<td>$person->Name</td>";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
    		<input type="submit" value="<?php echo $purpose;?>">
    		
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
    		    echo "<td>$person->Name</td>";
    		    
    		    echo "</tr>\n";
    		}
    		echo "</table>";

            ?>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>">
			</form>
        
        
        
	</div>


</body>
</html>
