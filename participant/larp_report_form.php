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

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-landmark"></i>
		Vad hände för <?php echo $name ?> på <?php echo $current_larp->Name; ?>
	</div>

	<form action="logic/larp_report_form_save.php" method="post">
    		<?php 
    		if (isset($role)) {
    		?>
    
    	<input type="hidden" id="RoleId" name="RoleId" value="<?php echo $role->Id; ?>">
    		<?php } else {?>
    	
    	<input type="hidden" id="GroupId" name="GroupId" value="<?php echo $group->Id; ?>">
    		<?php } ?>
    		
    	<div class='itemcontainer'>
    	För att vi arrangörer ska kunna göra nya spännande intriger som bygger på 
		det som har hänt behöver vi vet behöver vad just den här karaktären har varit med om.<br>
		Först kommer olika delar av din intrig så att du kan skriva om vad som hände i just den delen.<br>
		Efter det får du möjlighet att beskriva andra intressant saker som hände dig och andra.
		</div>
    		 
    		 <?php 
    		 
    		 
 			    foreach ($intrigueActors as $intrigueActor) {
 			        $intrigue = $intrigueActor->getIntrigue();
	                echo "<input type='hidden' id='IngtrigueActorId$intrigueActor->Id' name='IngtrigueActorId[]' value='$intrigueActor->Id'>";
	                echo "<div class='itemcontainer'>";
	                echo "<div class='itemname'><label for='IngtrigueActor_$intrigueActor->Id'>Vad hände med det här?</label></div>";

	                if (!empty($intrigue->CommonText)) echo nl2br(htmlspecialchars($intrigue->CommonText))."<br>";
	                echo nl2br(htmlspecialchars($intrigueActor->IntrigueText));
	                echo "<br>";
	                echo "<textarea id='IngtrigueActor_$intrigueActor->Id' name='IngtrigueActor_$intrigueActor->Id' rows='10' cols='100' maxlength='60000'>$intrigueActor->WhatHappened</textarea>";
	                echo "</div>";
		        }
            ?>
    		 
    		 

			<?php 
			if (isset($role)) {
			?>
				<div class='itemcontainer'>
	       		<div class='itemname'><label for="WhatHappened">Vad hände med/för <?php echo $role->Name; ?>?</label></div>
	       		Beskriv allt annat intressant som hände karaktären.  
				Skriv så mycket du kan. Det kan dessutom vara till hjälp om du ska spela karaktären vid ett 
				senare tillfälle.<br>
				<textarea id="WhatHappened" name="WhatHappened" rows="10" maxlength="60000"><?php echo htmlspecialchars($larp_role->WhatHappened); ?></textarea>
				</div>
			<?php } else {?>
				<div class='itemcontainer'>
	       		<div class='itemname'><label for="WhatHappened">Vad hände med/för <?php echo $group->Name; ?>?</label></div>
				Beskriv allt annat intressant som hände gruppen.  
				Skriv så mycket du kan. Det kan dessutom vara till hjälp nästa gång gruppen är med på ett lajv och när det kommer nya in i gruppen.<br>
				<textarea id="WhatHappened" name="WhatHappened" rows="10" maxlength="60000"><?php echo htmlspecialchars($larp_group->WhatHappened); ?></textarea>
				</div>
			<?php } ?>


			<?php 
			if (isset($role)) {
			?>
				<div class='itemcontainer'>
	       		<div class='itemname'><label for="WhatHappendToOthers">Vad såg du hände med andra?</label></div>
				Var du med om, eller såg något som hände en annan karkatär. Berätta! Vi vill veta allt. :)<br>
				<textarea id="WhatHappendToOthers" name="WhatHappendToOthers" rows="4" maxlength="60000"><?php echo htmlspecialchars($larp_role->WhatHappendToOthers); ?></textarea>
				</div>
				<?php } else {?>
				<div class='itemcontainer'>
	       		<div class='itemname'><label for="WhatHappendToOthers">Vad såg ni hände med andra?</label></div>
				Var ni med om, eller såg något som hände en annan karkatär eller grupp. Berätta! Vi vill veta allt. :)<br>
				<textarea id="WhatHappendToOthers" name="WhatHappendToOthers" rows="4" maxlength="60000"><?php echo htmlspecialchars($larp_group->WhatHappendToOthers); ?></textarea>
				</div>
			<?php } ?>

			 <div class='center'><input type="submit" class='button-18' value="Spara"></div>
		</form>
	</div>

</body>
</html>
