<?php

require 'header.php';

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
    header('Location: index.php?error=not_yours'); //Inte din karaktär
    exit;
}



include 'navigation.php';
?>



	<div class="content">
		<h1>Vad hände för <?php echo $role->Name; ?> på <?php echo $current_larp->Name; ?></h1>
		<form action="logic/larp_report_form_save.php" method="post">
    		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id; ?>">
    		<p>För att vi arrangörer ska kunna göra nya spännande intriger som bygger på 
    		det som har hänt behöver vi vet behöver vad just den här karaktären har varit med om.<br>
    		Först kommer olika delar av din intrig så att du kan skriva om vad som hände i just den delen.<br>
    		Efter det får du möjlighet att beskriva andra intressant saker som hände dig och andra.</p>
    		 
    		 <?php 
    		 
    		 
 			    $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
			    $intrigue_numbers = array();
		        foreach ($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<input type='hidden' id='IngtrigueActorId$intrigueActor->Id' name='IngtrigueActorId[]' value='$intrigueActor->Id'>";
		                echo "<div class='question'>";
		                echo "<label for='IngtrigueActor_$intrigueActor->Id'><strong>Vad hände med det här?</strong></label>";
		                echo "<div class='explanation'>";
		                echo nl2br($intrigueActor->IntrigueText);
		                echo "</div>";
		                echo "<textarea id='IngtrigueActor_$intrigueActor->Id' name='IngtrigueActor_$intrigueActor->Id' rows='10' cols='100' maxlength='60000'>$intrigueActor->WhatHappened</textarea>";
		                echo "</div>";
		            }
		        }
		                ?>
    		 
			<div class="question">
				<label for="WhatHappened"><strong>Vad hände med/för <?php echo $role->Name; ?>?</strong></label><br> 
				<div class="explanation">Beskriv allt annat intressant som hände karaktären.  
				Skriv så mycket du kan. Det kan dessutom vara till hjälp om du ska spela karaktären vid ett 
				senare tillfälle.</div>
				<textarea id="WhatHappened" name="WhatHappened" rows="10" cols="100" maxlength="60000"><?php echo htmlspecialchars($larp_role->WhatHappened); ?></textarea>
			</div>

			<div class="question">
				<label for="WhatHappendToOthers"><strong>Vad såg du hände med andra?</strong></label><br> 
				<div class="explanation">Var du med om, eller såg något som hände en annan karkatär. Berätta! Vi vill veta allt. :)</div>
				<textarea id="WhatHappendToOthers" name="WhatHappendToOthers" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($larp_role->WhatHappendToOthers); ?></textarea>
			</div>

			<input type="submit" value="Spara">
		</form>
	</div>

</body>
</html>
