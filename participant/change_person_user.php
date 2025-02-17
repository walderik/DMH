<?php

require 'header.php';

include 'navigation.php';
?>

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-user"></i>
		Ange nytt konto för <?php echo $current_person->Name;?>
	</div>

	<form action="logic/change_person_user_save.php" method="post">
			<div class='itemcontainer'>
	       	<div class='itemname'><label for="User">Nytt konto</label></div>
	       	Ange konto som ska hantera <?php echo $current_person->Name; ?>. Observera att du inte längre kommer att kunna hantera <?php echo $current_person->Name; ?> från det här kontot.
<br>
			<input type="text" id="UserEmail" name="UserEmail" size="100" maxlength="100" value='<?php echo $current_person->Email ?>' required>
			</div>

		    
			 <div class='center'><input type="submit" class='button-18' value="Spara"></div>

		</form>
	</div>

</body>
</html>