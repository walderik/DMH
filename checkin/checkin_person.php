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
        if (!$person->isRegistered($current_larp)) {
            header('Location: index.php'); // personen är inte anmäld
            exit;
        }
        $registration = Registration::loadByIds($person->Id, $current_larp->Id);
        $changed = false;
        if (isset($_POST['VehicleLicencePlate'])) {
            $registration->VehicleLicencePlate = strtoupper($_POST['VehicleLicencePlate']);
            $changed = true;
        }
        if (isset($_POST['doCheckin']) && !$registration->isCheckedIn()) {
            $now = new Datetime();
            $registration->CheckinTime =  date_format($now,"Y-m-d H:i:s");
            $changed = true;
        }
        if ($changed) $registration->update();
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

		<div class="header">
			<i class="fa-solid fa-user"></i>
			<?php echo $person->Name;?>
		</div>
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
				    echo "<a href='checkin_person.php?id=$guardian->Id'>$guardian->Name</a>"; 
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
		            $minor_str_arr[] = "<a href='checkin_person.php?id=$minor->Id'>$minor->Name</a>";
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
				<?php echo "<a href='view_house.php?$house->Id'>$house->Name</a>";  ?>
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

            <?php 
            $roles = $person->getRolesAtLarp($current_larp);
            if (!empty($roles)) {
            $one = sizeof($roles) == 1;
            
            ?>		    
	   		<div class='itemcontainer'>
           	<div class='itemname'>
           	<?php 
           	if ($one) echo "Karaktär";
            else "Karaktärer";
            ?>
           	
           	</div>
			<?php 

			foreach($roles as $role) {
			    echo "<div>";
			    echo "$role->Name";
			    if ($role->isMain($current_larp) && !$one) echo " (Huvudkaraktär)";
	
			    $group=$role->getGroup();
			    if (!empty($group)) echo " - $group->Name";
			    echo "<br>";
			    $checkin_letters = $role->getAllCheckinLetters($current_larp);
			    $checkin_telegrams = $role->getAllCheckinTelegrams($current_larp);
			    $checkin_props = $role->getAllCheckinProps($current_larp);
			    
			    $package = "";
			    
			    //Pengar
			    if (isset($larp_role->StartingMoney)) $package .= "$larp_role->StartingMoney $currency\n";
			    
			    
			    //Verksamheter
			    $titlededsArr = array();
			    $titledeeds = Titledeed::getAllForRole($role);
			    foreach ($titledeeds as $titledeed) {
			        if ($titledeed->isInUse() && $titledeed->IsFirstOwnerRole($role)) {
			            $titlededsArr[] = "  $titledeed->Name";
			        }
			    }
			    
			    if (!empty($titlededsArr)) $package .= "Verksamheter:\n".implode("\n", $titlededsArr)."\n";
			    
			    
			    
			    $docuumentsArr = array();
			    //Intrighandouts
			    $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
			    foreach ($intrigues as $intrigue) {
			        $intrgueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
			        $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
			        foreach($intrigue_Pdfs as $intrigue_Pdf) {
			            $docuumentsArr[] = "  $intrigue_Pdf->Filename";
			        }
			    }
			    
			    
			    //Brev
			    foreach($checkin_letters as $checkin_letter) {
			        $letter = $checkin_letter->getIntrigueLetter()->getLetter();
			        $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
			    }
			    
			    //Telegram
			    foreach($checkin_telegrams as $checkin_telegram) {
			        $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
			        $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
			    }
			    
			    //Dokument från grupperingar
			    $subdivisions = Subdivision::allForRole($role, $current_larp);
			    foreach ($subdivisions as $subdivision) {
			        //Intrighandouts
			        $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
			        foreach ($intrigues as $intrigue) {
			            $intrgueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
			            $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
			            foreach($intrigue_Pdfs as $intrigue_Pdf) {
			                $docuumentsArr[] = "  $intrigue_Pdf->Filename";
			            }
			            
			            $checkin_props_subdivision = $subdivision->getAllCheckinProps($current_larp);
			            $checkin_props = array_merge($checkin_props ,$checkin_props_subdivision);
			            
			            if ($subdivision->IsFirstRole($role, $current_larp)) {
			                
			                $checkin_letters_subdivision = $subdivision->getAllCheckinLetters($current_larp);
			                $checkin_telegrams_subdivision = $subdivision->getAllCheckinTelegrams($current_larp);
			                
			                
			                
			                //Brev
			                foreach($checkin_letters_subdivision as $checkin_letter) {
			                    $letter = $checkin_letter->getIntrigueLetter()->getLetter();
			                    $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
			                }
			                
			                //Telegram
			                foreach($checkin_telegrams_subdivision as $checkin_telegram) {
			                    $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
			                    $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
			                }
			            }
			            
			        }
			        
			        
			        
			    }
			    
			    
			    
			    
			    if (!empty($docuumentsArr)) $package .= "Dokument:\n".implode("\n", array_unique($docuumentsArr))."\n";
			    
			    //Props
			    $props_txt_Arr = array();
			    foreach($checkin_props as $checkin_prop) $props_txt_Arr[] = $checkin_prop->getIntrigueProp()->getProp()->Name;
			    if (!empty($props_txt_Arr)) $package .= "Rekvisita: ". implode(", ", $props_txt_Arr);
			    
			    //Magi
			    if ($current_larp->hasMagic()) {
			        if (Magic_Magician::isMagician($role)) {
			            $package .= "Magiker, ska ha sigill.\n";
			        }
			    }
			    
			    //Alkemi
			    if ($current_larp->hasAlchemy()) {
			        if (Alchemy_Supplier::isSupplier($role)) {
			            $supplier = Alchemy_Supplier::getForRole($role);
			            if ($supplier->hasIngredientList($current_larp) && $supplier->allAmountOfIngredientsApproved($current_larp)) $package .= "Lövjerist, ska ha sina etiketter.\n";
			        }
			        if (Alchemy_Alchemist::isAlchemist($role)) {
			            $alchemist = Alchemy_Alchemist::getForRole($role);
			            $recipes = $alchemist->getRecipes(true);
			            if (!empty($recipes)) $package .= "Alkemist, ska ha sina recept.\n";
			        }
			    }
			    
			    echo $package;
			    
			    
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
			    $checkin_letters = $group->getAllCheckinLetters($current_larp);
			    $checkin_telegrams = $group->getAllCheckinTelegrams($current_larp);
			    $checkin_props = $group->getAllCheckinProps($current_larp);
			    
			    $package = "";
			    
			    //Pengar
			    if (isset($larp_group->StartingMoney)) $package .= "$larp_group->StartingMoney $currency\n";
			    
			    //Verksamheter
			    $titlededsArr = array();
			    $titledeeds = Titledeed::getAllForGroup($group);
			    foreach ($titledeeds as $titledeed) {
			        if ($titledeed->IsFirstOwnerGroup($group)) {
			            $titlededsArr[] = "  $titledeed->Name";
			        }
			    }
			    if (!empty($titlededsArr)) $package .= "Verksamheter:\n".implode("\n", $titlededsArr)."\n";
			    
			    
			    
			    $docuumentsArr = array();
			    //Intrighandouts
			    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
			    foreach ($intrigues as $intrigue) {
			        $intrgueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
			        $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
			        foreach($intrigue_Pdfs as $intrigue_Pdf) {
			            $docuumentsArr[] = "  $intrigue_Pdf->Filename";
			        }
			    }
			    
			    
			    //Brev
			    foreach($checkin_letters as $checkin_letter) {
			        $letter = $checkin_letter->getIntrigueLetter()->getLetter();
			        $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
			    }
			    
			    //Telegram
			    foreach($checkin_telegrams as $checkin_telegram) {
			        $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
			        $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
			    }
			    if (!empty($docuumentsArr)) $package .= "Dokument:\n".implode("\n", $docuumentsArr)."\n";
			    
			    //Props
			    $props_txt_Arr = array();
			    foreach($checkin_props as $checkin_props) $props_txt_Arr[] = $checkin_props->getIntrigueProp()->getProp()->Name;
			    if (!empty($props_txt_Arr)) $package .= "Rekvisita: ". implode(", ", $props_txt_Arr);
			    
			    echo $package;
			}

			
			 ?>
        	
           	
           	
           	</div>

		    
		    
		    <?php } ?>
		    
	    
	    
	    	<?php if ($registration->isCheckedIn()) {?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Fordon</div>
               	<form method='POST'>
		    	<input type='hidden' id='person_id' name='person_id' value='<?php echo $person->Id ?>'>   
		    	<input type="text" id="VehicleLicencePlate" name="VehicleLicencePlate" value="<?php echo $registration->VehicleLicencePlate; ?>" size="10" maxlength="250"> 
				<input type="submit" value="Spara">
    			</form>
    			</div>
	    		
    	    	
    	    	
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Incheckad</div>
    			<?php echo $registration->CheckinTime;  ?>
    			</div>
	    	
	    	<?php } else { ?>
    	   		<div class='itemcontainer'>
               	<div class='itemname'>Bil registreringsnummer</div>
               	<form method='POST'>
		    	<input type='hidden' id='person_id' name='person_id' value='<?php echo $person->Id ?>'>   
		    	<input type='hidden' id='doCheckin' name='doCheckin' value='doCheckin'>   
		    	<input type="text" id="VehicleLicencePlate" name="VehicleLicencePlate" value="<?php echo $registration->VehicleLicencePlate; ?>" size="10" maxlength="250">
		    	<br><br> 
				<input type="submit" value="Checka in">

    			</div>
	    	
	    	
	    	
	    	<?php }?>	
	    
		    
		    
		    <!-- 

		    

		    
		    boende - länk till hus med alla andra boende, namn och karta

		
 -->

