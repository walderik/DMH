<?php

require 'header_subpage.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['id'])) {
       $role = Role::loadById($_GET['id']);
       $larp_role  = LARP_Role::loadByIds($_GET['id'], $current_larp->Id);
    } 
}

if (!isset($role) or !isset($larp_role)) {
    header('Location: index.php?error=not_');
    exit;   
}

if (Person::loadById($role->PersonId)->UserId != $current_user->Id) {
    header('Location: index.php?error=not_yours'); //Inte din roll
    exit;
}

?>

	<div class="content">
		<h1>Vad hände för <?php echo $role->Name; ?> på <?php echo $current_larp->Name; ?></h1>
		<form action="logic/larp_report_form_save.php" method="post">
    		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id; ?>">
    		<p>För att vi arrangörer ska kunna göra nya spännande intriger som bygger på 
    		det som har hänt behöver vi vet behöver vad just den här karaktären har varit med om.
    		 
			<div class="question">
				<label for="WhatHappened">Vad hände med/för <?php echo $role->Name; ?>?</label><br> 
				<div class="explanation">Beskriv allt intressant som hände karaktären. Vi vill självfallet 
				veta allt som relaterar till de intriger som karakären fick, men begränsa dig inte till det. 
				Skriv så mycket du kan. Det kan dessutom vara till hjälp om du ska spela karaktären vid ett 
				senare tillfälle.</div>
				<textarea id="WhatHappened" name="WhatHappened" rows="10" cols="100" maxlength="60000"><?php echo $larp_role->WhatHappened; ?></textarea>
			</div>

			<div class="question">
				<label for="WhatHappendToOthers">Vad såg du hände med andra?</label><br> 
				<div class="explanation">Var du med om, eller såg något som hände en annan karkatär. Berätta! Vi vill veta allt. :)</div>
				<textarea id="WhatHappendToOthers" name="WhatHappendToOthers" rows="4" cols="100" maxlength="60000"><?php echo $larp_role->WhatHappendToOthers; ?></textarea>
			</div>

			<input type="submit" value="Spara">
		</form>
	</div>

</body>
</html>
