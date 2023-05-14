<?php

require 'header.php';

$current_persons = $current_user->getPersons();

if (empty($current_persons)) {
    header('Location: index.php?error=no_person');
    exit;
}

$group = Group::newWithDefault();

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

if ($group->isRegistered($current_larp)) {
    header('Location: view_group.php?id='.$group->Id);
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
                $output = "Registrera";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}


include 'navigation.php';
?>

	<div class="content">
		<h1><?php 
		if ($operation == 'update') {
		    echo "Redigera $group->Name";
		} else {
		    echo "Registrering av en grupp";
		}    
		 ?></h1>
		<form action="logic/group_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php echo $group->Id; ?>">


			<p>En grupp är en gruppering av karaktärer som gör något tillsammans på
				lajvet. Exempelvis en familj på lajvet, en rånarliga eller ett rallarlag.<br><br>
				Det som du skriver i anmälan kommer att vara synligt för alla i gruppen, förrutom intrigidéer.</p>
				
				
			<h2>Gruppansvarig</h2>
			<p>Gruppansvarig är den som arrangörerna kommer att kontakta när det
				uppstår frågor kring gruppen.
			</p>
			
			<div class="question">
				<label for="Person">Gruppansvarig</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">Vem är gruppansvarig?</div>
				<?php selectionByArray('Person', $current_persons, false, true, $group->PersonId) ?>
			</div>
			
			
			
			<h2>Information om gruppen</h2>
			
			
			<div class="question">
				<label for="Name">Gruppens namn</label>&nbsp;<font style="color:red">*</font><br> 
				<input class="input_field" type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($group->Name); ?>" maxlength="20" required>
			</div>
			
			<div class="question">
    			<label for="Description">Beskrivning av gruppen</label>&nbsp;<font style="color:red">*</font><br>
    			<textarea class="input_field" id="Description" name="Description" rows="4" cols="50" maxlength="60000" required><?php echo htmlspecialchars($group->Description); ?></textarea>
			
			 
			</div>
			<div class="question">
    			<label for="DescriptionForOthers">Beskrivning av gruppen för andra</label>
				<div class="explanation">Vad är allmänt känt om gruppen? Beskriv sådant som de flesta vet om er. 
                         Ju mer du skriver deso troligare är det att andra kan hitta beröringspunkter med er och då får ni roligare spel.<br><br>
					     Efter att gruppen är anmäld kommer namnet på gruppen och den här beskrivningen 
					     att <a href="../participants.php?id=<?php echo $current_larp->Id;?>" target="_blank">synas för alla</a>. 
					     </div>
    			
    			<textarea class="input_field" id="DescriptionForOthers" name="DescriptionForOthers" rows="4" cols="50" maxlength="1000"><?php echo htmlspecialchars($group->DescriptionForOthers); ?></textarea>
			
			 
			</div>
			
			
			<div class="question">
				<label for="Friends">Vänner</label>
				<div class="explanation">Beskriv vilka gruppen anser vara sina vänner. Det vara både grupper och  beskrivning av egenskaper hos dem som är vänner. Exempelvis: Cheriffen, bankrånare och telegrafarbetare</div>
				<textarea class="input_field" id="Friends" name="Friends" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->Friends); ?></textarea>
			</div>
			<div class="question">
				<label for="Enemies">Fiender</label>
				<div class="explanation">Beskriv vilka gruppen anser vara sina fiender. Det vara både grupper och  beskrivning av egenskaper hos dem som är fiender. Exempelvis: Guldletare, Big Bengt och alla som gillar öl.</div>
				<textarea class="input_field" id="Enemies" name="Enemies" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->Enemies); ?></textarea>
			</div>


			<div class="question">
			<label for="Wealth">Hur rik anser du att gruppen är?</label>&nbsp;<font style="color:red">*</font>
			<div class="explanation"><?php Wealth::helpBox($current_larp); ?></div>

			
            <?php

            Wealth::selectionDropdown($current_larp, false, true, $group->WealthId);
            
            ?> 
			
			
			</div>
			<div class="question">
			<label for="PlaceOfResidence">Var bor gruppen?</label>&nbsp;<font style="color:red">*</font>
			<div class="explanation">Tänk typ folkbokföringsadress, dvs även om gruppen tillfälligt är i Slow River så vill vi veta var gruppen har sitt hem.<br><?php PlaceOfResidence::helpBox($current_larp); ?></div>
			
			
            <?php
            PlaceOfResidence::selectionDropdown($current_larp, false, true, $group->PlaceOfResidenceId);
            ?> 

			</div>
			
					
			<div class="question">
			<label for="IntrigueIdeas">Intrigidéer</label>
			<div class="explanation">
			Har ni några grupprykten som ni vill ha hjälp med att sprida? <br>Denna del är inte synlig för medlemmarna i gruppen.
			</div>
			<textarea class="input_field" id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->IntrigueIdeas); ?></textarea>
			
			
			</div>
						
			<div class="question">
			<label for="OtherInformation">Något annat arrangörerna bör veta om er grupp?</label><br>
			<textarea class="input_field" id="OtherInformation" name="OtherInformation" rows="4" cols="50" maxlength="60000"><?php echo htmlspecialchars($group->OtherInformation); ?></textarea>
			
			 
			</div>
			

			  <input type="submit" name="action" value="<?php default_value('action'); ?>">
			  <?php if ($current_larp->RegistrationOpen == 1) { ?>
			  <input type="submit" name="action" value="<?php default_value('action'); ?> och gå direkt till anmälan">
			  <?php }?>
		</form>
	</div>

</body>
</html>