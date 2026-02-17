<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $person = Person::loadById($_GET['id']);
        if (!$person->isRegistered($current_larp)) {
            header('Location: index.php'); // personen är inte anmäld
            exit;
        }
        $registration = Registration::loadByIds($person->Id, $current_larp->Id);
    }
    else {
        header('Location: index.php');
        exit;
    }
}

    

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['person_id'])) {
        $person = Person::loadById($_POST['person_id']);
        if (empty($person) || !$person->isRegistered($current_larp)) {
            header('Location: index.php'); // personen är inte anmäld
            exit;
        }
        $registration = Registration::loadByIds($person->Id, $current_larp->Id);

        
        if (isset($_POST['saveMoneyRole'])) {
            $larp_role = LARP_Role::loadByIds($_POST['roleId'], $current_larp->Id);
            $larp_role->EndingMoney = $_POST['EndingMoney'];
            $larp_role->update();
        }
        if (isset($_POST['saveMoneyGroup'])) {
            $larp_group = LARP_Group::loadByIds($_POST['groupId'], $current_larp->Id);
            $larp_group->EndingMoney = $_POST['EndingMoney'];
            $larp_group->update();
        }
        
        if (isset($_POST['doCheckout']) && !$registration->isCheckedOut()) {
            $now = new Datetime();
            $registration->CheckoutTime =  date_format($now,"Y-m-d H:i:s");
            $registration->update();
        }
    }
    else {
        header('Location: index.php');
        exit;
    }
}






$campaign = $current_larp->getCampaign();
$currency = $campaign->Currency;

include 'navigation.php';
?>

<style>
.roleContainer {
    margin-bottom: 5x;
    padding-bottom: 5px;
    border-width: 0 0 1px 0 ;
    border-style: solid;
}
.roleContainer:last-child {
    border:none;
}

.money {
    width: 60px;
    text-align: right;
}

</style>

		<div class="header">
			<i class="fa-solid fa-user"></i>
			Utcheckning av <?php echo $person->Name;?>
		</div>
   		<div class='itemcontainer'>
			<a href="checkin_person.php?id=<?php echo $person->Id ?>">Gå till incheckning</a>
		</div>
		<?php 
		$mainRole = $person->getMainRole($current_larp);
		if (!empty($mainRole) && $mainRole->hasImage()) {
		    echo "<div class='itemcontainer'>";
		    echo "<img width='200' src='../includes/display_image.php?id=$mainRole->ImageId'/>\n";
		    echo "</div>";
		}
		
		?>
   		<div class='itemcontainer'>
       	<div class='itemname'>Ålder</div>
			<?php echo $person->getAgeAtLarp($current_larp);?>
		</div>
				
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $campaign->MinimumAgeWithoutGuardian) {
		    ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Ansvarig vuxen</div>
				<?php 
				if (!empty($registration->GuardianId)) {
				    $guardian = $registration->getGuardian();
				    echo "<a href='checkout_person.php?id=$guardian->Id'>$guardian->Name</a>"; 
				} else echo showStatusIcon(false); ?>
    			</div>
		    <?php 
		    }
		    ?>

		    <?php 
		    $minors = $person->getGuardianFor($current_larp);
		    if (!empty($minors)) {
		        echo "<div class='itemcontainer'>";
		        echo "<div class='itemname'>Ansvarig vuxen för</div>";
		        $minor_str_arr = array();
		        foreach ($minors as $minor) {
		            $minor_str_arr[] = "<a href='checkout_person.php?id=$minor->Id'>$minor->Name</a>";
		        }
		        echo implode(", ", $minor_str_arr);
		        echo "</div>";
		    }
		    ?>
		    
		    
		    
		    
		    <?php 
		    $house = House::getHouseAtLarp($person, $current_larp);
		    if (!empty($house)) {
	        ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Boende</div>
				<?php echo "<a href='view_house.php?id=$house->Id&action=checkout'>$house->Name</a>";  ?>
    			</div>
		     
		    <?php     
		    }
		    ?>
		    <?php if (isset($registration->FoodChoice)) { ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Matalternativ</div>
				<?php echo $registration->FoodChoice;  ?>
    			</div>

			<?php } ?>		
			
   	   		<div class='itemcontainer'>
           	<div class='itemname'>Fordon</div>
           	<?php echo $registration->VehicleLicencePlate; ?>
			</div>
	    		
			    

            <?php 
            $roles = $person->getRolesAtLarp($current_larp);
            if (!empty($roles)) {
            $one = sizeof($roles) == 1;
            
            ?>		    
	   		<div class='itemcontainer'>
           	<div class='itemname'>
           	<?php 
           	if ($one) echo "Karaktär";
            else echo "Karaktärer";
            ?>
           	
           	</div>
			<?php 

			foreach($roles as $role) {
			    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
			    echo "<div class='roleContainer'>";
			    echo "$role->Name";
			    if ($larp_role->IsMainRole==0) echo " (Sidokaraktär)";
	
			    $group=$role->getGroup();
			    if (!empty($group)) echo " - $group->Name";
			    echo "<br>";
			    
			    //Pengar
			    if (isset($larp_role->StartingMoney)) {
			        echo "Började med $larp_role->StartingMoney $currency<br>\n";
			        
			        echo "<form method='POST'>";
			        echo "Slutade med ";
			        echo "<input type='hidden' name='person_id' value='$person->Id'>";
			        echo "<input type='hidden' name='roleId' value='$role->Id'>";
			        echo "<input type='hidden' name='saveMoneyRole' value='saveMoneyRole'>";
			        echo "<input class='money' type='number' name='EndingMoney' value='$larp_role->EndingMoney' size='5' maxlength='20'> $currency ";
			        echo "<input type='submit' value='Spara'>";
                    echo "</form>";

			    }
			    
			    //Props
			    $checkin_props = $role->getAllCheckinProps($current_larp);
			    $props_txt_Arr = array();
			    foreach($checkin_props as $checkin_prop) $props_txt_Arr[] = $checkin_prop->getIntrigueProp()->getProp()->Name;
			    if (!empty($props_txt_Arr)) echo "Rekvisita: ". implode(", ", $props_txt_Arr);
			    
			    echo "</div>";
			}
			
			 ?>
			</div>
			<?php } ?>

		    <?php 
		    $groups = Group::getAllRegisteredGroupsForPerson($person->Id, $current_larp);
		    if (!empty($groups)) {
		        
		    ?>
	   		<div class='itemcontainer'>
           	<div class='itemname'>Gruppledare för</div>
  
			<?php 
            $first = true;
			foreach($groups as $group) {
			    if ($first) $first = false;
			    else echo "<br>";
			    echo "$group->Name";
			    echo "<br>";
			    
			    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
			    $checkin_props = $group->getAllCheckinProps($current_larp);
			    
			    $package = "";
			    
			    //Pengar
			    if (isset($larp_group->StartingMoney)) {
			        echo "Började med $larp_group->StartingMoney $currency<br>\n";
			        
			        
			        echo "<form method='POST'>";
			        echo "Slutade med ";
			        echo "<input type='hidden' name='person_id' value='$person->Id'>";
			        echo "<input type='hidden' name='groupId' value='$group->Id'>";
			        echo "<input type='hidden' name='saveMoneyGroup' value='saveMoneyRole'>";
			        echo "<input class='money' type='number' name='EndingMoney' value='$larp_group->EndingMoney' size='5' maxlength='20'> $currency ";
			        echo "<input type='submit' value='Spara'>";
			        echo "</form>";
			    }
			    
			    //Verksamheter
			    $titlededsArr = array();
			    $titledeeds = Titledeed::getAllForGroup($group);
			    foreach ($titledeeds as $titledeed) {
			        if ($titledeed->IsFirstOwnerGroup($group)) {
			            $titlededsArr[] = "  $titledeed->Name";
			        }
			    }
			    if (!empty($titlededsArr)) $package .= "Verksamheter:\n".implode("\n", $titlededsArr)."\n";
			    
			    
			    
			    //Props

			    $props_txt_Arr = array();
			    foreach($checkin_props as $checkin_props) $props_txt_Arr[] = $checkin_props->getIntrigueProp()->getProp()->Name;
			    if (!empty($props_txt_Arr)) $package .= "Rekvisita: ". implode(", ", $props_txt_Arr);
			    
			    echo $package;
			}

			
			 ?>
        	
           	
           	
           	</div>

		    
		    
		    <?php } ?>
		    
	    
	    
	    	<?php if ($registration->isCheckedOut()) {?>
     	    	
    	    	
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Utcheckad</div>
    			<?php echo $registration->CheckoutTime;  ?>
    			</div>
	    	
	    	<?php } else { ?>
    	   		<div class='itemcontainer'>
               	<form method='POST'>
		    	<input type='hidden' id='person_id' name='person_id' value='<?php echo $person->Id ?>'>   
		    	<input type='hidden' id='doCheckout' name='doCheckout' value='doCheckout'>   
				<input type="submit" value="Checka ut">

    			</div>
	    	
	    	
	    	
	    	<?php }?>	
	    
		    
		    
		    <!-- 

		    

		    
		    boende - länk till hus med alla andra boende, namn och karta

		
 -->

