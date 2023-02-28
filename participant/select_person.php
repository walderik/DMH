<?php

require 'header.php';

$current_persons = $current_user->getUnregisteredPersonsForUser($current_larp);

if (empty($current_persons)) {
    header('Location: index.php?error=no_person');
    exit;
}

if (count($current_persons) == 1) {
    header('Location: person_registration_form.php?PersonId='. $current_persons[0]->Id);
    exit;
    
}

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>

	<div class="content">
		<h1>Anmälan av deltagare till <?php echo $current_larp->Name;?></h1>
		<?php
		    $i = 0;
		    $possible_persons = Array();
		    foreach ($current_persons as $person) {
    	       if (empty($person->getRoles())) {
    	           echo "<br><b>$person->Name</b> saknar en karaktär och kan inte anmälas ännu.<br><br>\n";   	           
                }
                else {
                    array_push($possible_persons,$person);
                }
            }
//             reset($current_persons);
    	?>
		<form action="person_registration_form.php" method="get">
			<div class="question">
				<label for="PersonId">Deltagare</label><br>
				<div class="explanation">Vilken deltagare vill du anmäla?</div>
				<?php selectionDropdownByArray('Person', $possible_persons, false, true) ?>
			</div>
			  <input type="submit" value="Välj">

		</form>
	</div>

</body>
</html>