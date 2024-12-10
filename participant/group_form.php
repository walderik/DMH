<?php

require 'header.php';

$admin = false;
if (isset($_GET['admin'])) $admin = true;

if (empty($current_person) && !$admin) {
    header('Location: index.php');
    exit;
}

$group = Group::newWithDefault();
$group->PersonId = $current_person->Id;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $group = Group::loadById($_GET['id']);
    } else {
    }
}

if ($group->isRegistered($current_larp) && !$group->userMayEdit($current_larp)) {
    header('Location: ' . $group->getViewLink());
    exit;
}

function default_value($field) {
    GLOBAL $group;
    $output = "";
    
    switch ($field) {
        case "operation":
            if (is_null($group->Id)) {
                $output = "insert";
                break;
            }
            $output = "update";
            break;
        case "action":
            if (is_null($group->Id)) {
                $output = "Skapa";
                break;
            }
            $output = "Ändra";
            break;
    }
    
    echo $output;
}


include 'navigation.php';
?>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-people-group"></i>
		<?php 
		if ($operation == 'update') {
		    echo "Ändra $group->Name";
		} else {
		    echo "Skapa en grupp";
		}    
		 ?>	</div>
    
		<div class='itemcontainer'>
			En grupp är en gruppering av karaktärer som gör något tillsammans på
				lajvet. Exempelvis en familj på lajvet, en rånarliga eller ett rallarlag.<br><br>
				Det som du skriver i anmälan kommer att vara synligt för alla i gruppen, förrutom intrigidéer.
		</div>


		<form action="logic/group_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php echo $group->Id; ?>">
   			<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $group->PersonId; ?>">

			<div class='itemcontainer'>
	       	<div class='itemname'>Gruppens namn <font style="color:red">*</font></div>
			<input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($group->Name); ?>" maxlength="20" required>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'>Beskrivning av gruppen <font style="color:red">*</font></div>
			<textarea id="Description" name="Description" rows="4" cols="50" maxlength="60000" required><?php echo htmlspecialchars($group->Description); ?></textarea>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'>Beskrivning av gruppen för andra</div>
	       	Vad är allmänt känt om gruppen? Beskriv sådant som de flesta vet om er. 
                         Ju mer du skriver deso troligare är det att andra kan hitta beröringspunkter med er och då får ni roligare spel.<br><br>
					     Efter att gruppen är anmäld och godkänd kommer namnet på gruppen och den här beskrivningen 
					     att synas för alla som har plats på lajvet.<br>
			<textarea id="DescriptionForOthers" name="DescriptionForOthers" rows="4" cols="50" maxlength="1000"><?php echo htmlspecialchars($group->DescriptionForOthers); ?></textarea>
			</div>

			<div class='itemcontainer'>
	       	<div class='itemname'>Vänner</div>
	       	Beskriv vilka gruppen anser vara sina vänner. Det vara både grupper och  beskrivning av egenskaper hos dem som är vänner.<br>
			<textarea id="Friends" name="Friends" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->Friends); ?></textarea>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'>Fiender</div>
	       	Beskriv vilka gruppen anser vara sina fiender. Det vara både grupper och  beskrivning av egenskaper hos dem som är fiender.<br>
			<textarea id="Enemies" name="Enemies" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->Enemies); ?></textarea>
			</div>
			

			<?php if (GroupType::isInUse($current_larp)) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'>Typ av grupp<font style="color:red">*</font></div>
    	       	Vad är ni för typ av grupp? Vad är det som gör att ni är en enhet?<br>
                <?php
                GroupType::selectionDropdown($current_larp, false, true, $group->GroupTypeId);
                ?> 
    			</div>
			<?php } ?>
			
			<?php if (ShipType::isInUse($current_larp)) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'>Typ av skepp <font style="color:red">*</font></div>
    	       	Vilken typ av skepp seglar ni?<br>
                <?php
                ShipType::selectionDropdown($current_larp, false, true, $group->ShipTypeId);
                ?> 
    			</div>
			<?php } ?>

			<?php if ($current_larp->getCampaign()->is_me()) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'>Färg&nbsp;<font style="color:red">*</font></div>
    	       	Som besättning kommer ni tilldelas en färg som skall symboliserar er både fysiskt i dräkt och på ert skepp. 
    			Färgen kan ses som ”gängsymbol” och bärs med stolthet. Färgen följer med er besättning så länge ni är aktiva i kampanjen. <br>
    			<br>
    			Här anmäler du vilken färg besättningen önskar. Rangordna dem i 1-3. Tex: 1.röd 2. Blå 3. Rostbrun. Vi kommer göra en första utdelning av färger i slutet av varje månad.<br><br>
    			Har ni en färg sedan tidigare lajv låter ni den stå kvar utan att ändra. <br>
                <input type="text" id="Colour" name="Colour" value="<?php echo htmlspecialchars($group->Colour); ?>" maxlength="250" required>
    			</div>
			<?php } ?>

 			<?php if (Wealth::isInUse($current_larp)) { ?>
    			<div class='itemcontainer'>
    	       	<div class='itemname'>Hur rik är gruppen?&nbsp;<font style="color:red">*</font></div>
                <?php
                Wealth::selectionDropdown($current_larp, false, true, $group->WealthId);
                ?> 
    			</div>
			<?php } ?>
			
			<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
   				<div class='itemcontainer'>
    	       	<div class='itemname'>Var bor gruppen?&nbsp;<font style="color:red">*</font></div>
    	       	Tänk typ folkbokföringsadress, dvs även om gruppen tillfälligt är på platsen så vill vi veta var gruppen har sitt hem.<br>
                <?php
                PlaceOfResidence::selectionDropdown($current_larp, false, true, $group->PlaceOfResidenceId);
                ?> 
    			</div>
			<?php } ?>
			
			<?php if (IntrigueType::isInUseForGroup($current_larp)) { ?>
   				<div class='itemcontainer'>
    	       	<div class='itemname'>Intrigtyper</div>
    	       	Vilken typ av intriger vill gruppen helst ha?<br>
                <?php
                IntrigueType::selectionDropdownGroup($current_larp, true, false, $group->getSelectedIntrigueTypeIds());
                ?> 
    			</div>
			<?php } ?>
			

			<div class='itemcontainer'>
	       	<div class='itemname'>Intrigidéer</div>
	       	Har ni några idé för intriger som skulle passa gruppen? <br>Denna del är inte synlig för medlemmarna i gruppen.<br>
			<textarea id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->IntrigueIdeas); ?></textarea>
			</div>
			
			<div class='itemcontainer'>
	       	<div class='itemname'>Något annat arrangörerna bör veta om er grupp?</div>
			<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->OtherInformation); ?></textarea>
			</div>
			 


			<?php 
			if ($admin) {
			    //Om bara tittar på formuläret som arrangör får man inte lyckas skicka in
			    $type = "button";
			} else {
			    $type = "submit";
		    }
		    
			    ?>
			
			<div class='center'>
			  <input type="<?php echo $type ?>" class='button-18' name="action" value="<?php default_value('action'); ?>">
			  <?php if ($current_larp->mayRegister() && !$group->isRegistered($current_larp)) { ?>
			  <input type="<?php echo $type ?>" class='button-18' name="action" value="<?php default_value('action'); ?> och gå direkt till anmälan">
			  <?php }?>
		    </div>
		</form>
	</div>

</body>
</html>