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



?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


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


			<p>En grupp är en gruppering av roller som gör något tillsammans på
				lajvet. Exempelvis en familj på lajvet, en rånarliga eller ett rallarlag.</p>
				
				
			<h2>Gruppansvarig</h2>
			<p>Gruppansvarig är den som arrangörerna kommer att kontakta när det
				uppstår frågor kring gruppen.
			</p>
			
			<div class="question">
				<label for="Person">Gruppansvarig</label><br>
				<div class="explanation">Vem är gruppansvarig?</div>
				<?php selectionDropdownByArray('Person', $current_persons, false, true, $group->PersonId) ?>
			</div>
			
			
			
			<h2>Information om gruppen</h2>
			
			
			<div class="question">
				<label for="Name">Gruppens namn</label><br> 
				<input type="text" id="Name" name="Name" value="<?php echo $group->Name; ?>" required>
			</div>
			
			<div class="question">
    			<label for="Description">Beskrivning av gruppen</label><br>
    			<textarea id="Description" name="Description" rows="4" cols="50"><?php echo $group->Description; ?></textarea>
			
			 
			</div>
			
			
			<div class="question">
				<label for="Friends">Vänner</label><br>
				<textarea id="Friends" name="Friends" rows="4" cols="50"><?php echo $group->Friends; ?></textarea>
			</div>
			<div class="question">
				<label for="Enemies">Fiender</label><br>
				<textarea id="Enemies" name="Enemies" rows="4" cols="50"><?php echo $group->Enemies; ?></textarea>
			</div>


			<div class="question">
			<label for="Wealth">Hur rik anser du att ni är?</label>
			<div class="explanation"><?php Wealth::helpBox(true); ?></div>

			
            <?php

            Wealth::selectionDropdown(false, false, $group->WealthId);
            
            ?> 
			
			
			</div>
			<div class="question">
			<label for="PlaceOfResidence">Var bor gruppen?</label>
			<div class="explanation"><?php PlaceOfResidence::helpBox(true); ?></div>
			
			
            <?php
            PlaceOfResidence::selectionDropdown(false, false, $group->PlaceOfResidenceId);
            ?> 

			</div>
			
					
			<div class="question">
			<label for="IntrigueIdeas">Intrigidéer</label>
			<div class="explanation">
			Har ni några grupprykten som ni vill ha hjälp med att sprida? 
			</div>
			<textarea id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="50"><?php echo $group->IntrigueIdeas; ?></textarea>
			
			
			</div>
						
			<div class="question">
			<label for="OtherInformation">Något annat arrangörerna bör veta om er grupp?</label><br>
			<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="50"><?php echo $group->OtherInformation; ?></textarea>
			
			 
			</div>
			
			
			<div class="question">
			Genom att kryssa i denna ruta så lovar jag med
			heder och samvete att jag har läst igenom alla hemsidans regler och
			förmedlat dessa till mina gruppmedlemmar. Vi har även alla godkänt
			dem och är införstådda med vad som förväntas av oss som grupp av
			deltagare på lajvet. Om jag inte har läst reglerna så kryssar jag
			inte i denna ruta.<br>
			
			<input type="checkbox" id="rules" name="rules" value="Ja" required>
  			<label for="rules">Jag lovar</label> 
			</div>

			  <input type="submit" value="<?php default_value('action'); ?>">
		</form>
	</div>

</body>
</html>