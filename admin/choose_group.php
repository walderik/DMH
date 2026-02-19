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

if ($operation == "set_prop_owner_group") {
    $purpose = "Sätt ägare av rekvisita";
    $url = "prop_owner_form.php";
}
elseif ($operation == "add_titledeed_owner_group") {
    $purpose = "Sätt som ägare av verksamhet";
    $url = "logic/titledeed_form_save.php";
    $multiple=true;
}
elseif ($operation == "add_intrigue_actor_group") {  
    $purpose = "Lägg till aktör i intrig";
    $url = "logic/view_intrigue_logic.php";
    $multiple=true;
}
elseif ($operation == "exhange_intrigue_actor_group") {
    $purpose = "Byt aktör på intrig";
    $url = "logic/view_intrigue_logic.php";
} elseif ($operation == "add_concerns_group") {
    $purpose = "Lägg till grupp(er) som ryktet handlar om";
    $url = "rumour_form.php";
    $multiple=true;
} elseif ($operation == "add_knows_group") {
    $purpose = "Lägg till grupp(er) som känner till ryktet";
    $url = "rumour_form.php";
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

<script src="../javascript/show_hide_rows.js"></script>



    <div class="content">   
        <h1><?php echo $purpose;?></h1>
     		<?php 
     		if (isset($_GET['notRegistered'])) $groups = Group::getAllUnregisteredGroups($current_larp);
    		else $groups = Group::getAllRegistered($current_larp);
    		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
    		} else {
    		    ?>
    		    <?php 
    		    if (!empty($intrigueTypes)) {
    		        echo "Grupper filtrerade på intrigtyper ". commaStringFromArrayObject($intrigue->getIntrigueTypes())."<br>";
    		        echo '<button id="btn_show" onclick="show_hide();">Visa alla</button>';
    		        echo "<br><br>";
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
    		    <table class='data'>
    		    <tr><th>Namn</th><th>Vill ha<br>intrig</th>
    		    <?php if (IntrigueType::isInUse($current_larp)) echo "<th>Intrigtyper</th>"; ?>
    		    </tr>
    		    <?php 
    		    foreach ($groups as $group)  {
    		        $show = true;
    		        $larp_group=LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        if (!empty($intrigueTypes)) {
    		            $group_intrigueTypeIds = $larp_group->getSelectedIntrigueTypeIds();
    		            if (empty(array_intersect($intrigueTypes, $group_intrigueTypeIds))) {
    		                $show = false;
    		            }
    		        }
    		        if ($show) echo "<tr>\n";
    		        else echo "<tr class='show_hide hidden'>\n";
    		        echo "<td><input type='$type' id='Group$group->Id' name='GroupId$array' value='$group->Id'>";

    		        echo "<label for='Group$group->Id'>$group->Name</label></td>\n";
    		        echo "<td>";
    		        if (isset($larp_group)) echo ja_nej($larp_group->WantIntrigue);
    		        echo "</td>\n";
    		        if (IntrigueType::isInUse($current_larp)) {
    		            echo "<td>";
    		            if (isset($larp_group)) echo commaStringFromArrayObject($larp_group->getIntrigueTypes());
        		        echo "</td>";
    		        }
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>">
        
        	<?php 
        	$npc_groups = Group::getAllHiddenGroups($current_larp->CampaignId);
        	if (empty($npc_groups)) {
    		} else {
    		    
    		    ?>
    		    <h2>Gömda grupper</h2>
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
    		    <table class='data'>
    		    <tr><th>Namn</th>
    		    </tr>
    		    <?php 
    		    foreach ($npc_groups as $group)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Group$group->Id' name='GroupId$array' value='$group->Id'>";

    		        echo "<label for='Group$group->Id'>$group->Name</label></td>\n";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>"></form>
        
        
	</div>
</body>

</html>
