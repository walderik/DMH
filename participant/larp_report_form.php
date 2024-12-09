<?php

require 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['roleId'])) {
       $role = Role::loadById($_GET['roleId']);
       $name = $role->Name;
       $larp_role  = LARP_Role::loadByIds($role->Id, $current_larp->Id);
       
       
       if ($role->PersonId != $current_person->Id) {
           header('Location: index.php?error=not_yours'); //Inte din karaktär
           exit;
       }
       
       $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
       $intrigueActors = array();
       foreach ($intrigues as $intrigue) {
           $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
           if ($intrigue->isActive() && (!empty($intrigue->CommonText) || !empty($intrigueActor->IntrigueText))) {
               $intrigueActors[] = $intrigueActor;
           }
           
       }
       
    } elseif (isset($_GET['groupId'])) {
        $group = Group::loadById($_GET['groupId']);
        $name = $group->Name;
        $larp_group  = LARP_Group::loadByIds($group->Id, $current_larp->Id);
        
        
        if (!$current_person->isGroupLeader($group)) {
            header('Location: index.php'); //Inte din grupp
            exit;
        }
        
        $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
        $intrigueActors = array();
        foreach ($intrigues as $intrigue) {
            $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
            if ($intrigue->isActive() && (!empty($intrigue->CommonText) || !empty($intrigueActor->IntrigueText))) {
                $intrigueActors[] = $intrigueActor;
            }
            
        }
    }
        
}

     

if (!isset($intrigueActors) or (!isset($larp_role) and !isset($larp_group))) {
    header('Location: index.php');
    exit;
}
        
include 'navigation.php';
?>

<style>

textarea{
    
    width: 100%;
}
</style>


	<div class="content">
		<h1>Vad hände för <?php echo $name ?> på <?php echo $current_larp->Name; ?></h1>
		<form action="logic/larp_report_form_save.php" method="post">
				<?php 
				if (isset($role)) {
				?>
		
    		<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id; ?>">
				<?php } else {?>
    		
    		<input type="hidden" id="GroupId" name="GroupId" value="<?php echo $group->Id; ?>">
				<?php } ?>
    		
    		<p>För att vi arrangörer ska kunna göra nya spännande intriger som bygger på 
    		det som har hänt behöver vi vet behöver vad just den här karaktären har varit med om.<br>
    		Först kommer olika delar av din intrig så att du kan skriva om vad som hände i just den delen.<br>
    		Efter det får du möjlighet att beskriva andra intressant saker som hände dig och andra.</p>
    		 
    		 <?php 
    		 
    		 
 			    foreach ($intrigueActors as $intrigueActor) {
 			        $intrigue = $intrigueActor->getIntrigue();
	                echo "<input type='hidden' id='IngtrigueActorId$intrigueActor->Id' name='IngtrigueActorId[]' value='$intrigueActor->Id'>";
	                echo "<div class='question'>";
	                echo "<label for='IngtrigueActor_$intrigueActor->Id'><strong>Vad hände med det här?</strong></label>";
	                echo "<div class='explanation'>";
	                if (!empty($intrigue->CommonText)) echo nl2br(htmlspecialchars($intrigue->CommonText))."<br>";
	                echo nl2br(htmlspecialchars($intrigueActor->IntrigueText));
	                echo "</div>";
	                echo "<textarea id='IngtrigueActor_$intrigueActor->Id' name='IngtrigueActor_$intrigueActor->Id' rows='10' cols='100' maxlength='60000'>$intrigueActor->WhatHappened</textarea>";
	                echo "</div>";
		        }
		                ?>
    		 
    		 
			<div class="question">
				<?php 
				if (isset($role)) {
				?>
				<label for="WhatHappened"><strong>Vad hände med/för <?php echo $role->Name; ?>?</strong></label><br> 
				<div class="explanation">Beskriv allt annat intressant som hände karaktären.  
				Skriv så mycket du kan. Det kan dessutom vara till hjälp om du ska spela karaktären vid ett 
				senare tillfälle.</div>
				<textarea id="WhatHappened" name="WhatHappened" rows="10" maxlength="60000"><?php echo htmlspecialchars($larp_role->WhatHappened); ?></textarea>
				<?php } else {?>
				<label for="WhatHappened"><strong>Vad hände med/för <?php echo $group->Name; ?>?</strong></label><br> 
				<div class="explanation">Beskriv allt annat intressant som hände gruppen.  
				Skriv så mycket du kan. Det kan dessutom vara till hjälp nästa gång gruppen är med på ett lajv och när det kommer nya in i gruppen.</div>
				<textarea id="WhatHappened" name="WhatHappened" rows="10" maxlength="60000"><?php echo htmlspecialchars($larp_group->WhatHappened); ?></textarea>
				<?php } ?>
			</div>

			<div class="question">
				<?php 
				if (isset($role)) {
				?>
				<label for="WhatHappendToOthers"><strong>Vad såg du hände med andra?</strong></label><br> 
				<div class="explanation">Var du med om, eller såg något som hände en annan karkatär. Berätta! Vi vill veta allt. :)</div>
				<textarea id="WhatHappendToOthers" name="WhatHappendToOthers" rows="4" maxlength="60000"><?php echo htmlspecialchars($larp_role->WhatHappendToOthers); ?></textarea>
				<?php } else {?>
				<label for="WhatHappendToOthers"><strong>Vad såg ni hände med andra?</strong></label><br> 
				<div class="explanation">Var ni med om, eller såg något som hände en annan karkatär eller grupp. Berätta! Vi vill veta allt. :)</div>
				<textarea id="WhatHappendToOthers" name="WhatHappendToOthers" rows="4" maxlength="60000"><?php echo htmlspecialchars($larp_group->WhatHappendToOthers); ?></textarea>
				<?php } ?>
			</div>

			<input type="submit" value="Spara">
		</form>
	</div>

</body>
</html>
