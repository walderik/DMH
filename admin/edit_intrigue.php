<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $RoleId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$role = Role::loadById($RoleId);

$isRegistered = $role->isRegistered($current_larp);

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

$person = $role->getPerson();

include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $role->Name;?>&nbsp;<a href='edit_role.php?id=<?php echo $role->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<div>
				<?php include 'print_role.php';?>
		</div>
		
		<?php  if ($isRegistered) {?>
		<h2>Intrig</h2>
				<?php 
		$intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
		if (!empty($intrigues)) {
		    echo "<table class='data'>";
		    echo "<tr><th>Intrig</th><th>Intrigtext</th></tr>";
	        foreach ($intrigues as $intrigue) {
	           echo "<tr>";
	           echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a></td>";
	           $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
	           echo "<td>$intrigueActor->IntrigueText</td>";
	           echo "</tr>";
	       }
	       echo "</table>";
	       echo "<br>";
		}
	    ?>
		
		<form action="logic/edit_intrigue_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<textarea id="Intrigue" name="Intrigue" rows="20" cols="150" maxlength="60000"><?php    echo htmlspecialchars($larp_role->Intrigue); ?></textarea><br>
		
		<input type="submit" value="Spara">
		</form>

		<?php } ?>
		<h2>Anteckningar (visas inte för deltagaren)</h2>

		<form action="logic/edit_intrigue_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">

		<textarea id="OrganizerNotes" name="OrganizerNotes" rows="20" cols="150" maxlength="60000"><?php    echo htmlspecialchars($role->OrganizerNotes); ?></textarea><br>
		
		<input type="submit" value="Spara">

			</form>
		<?php 
		$previous_larps = $role->getPreviousLarps();
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    
		    echo "<h2>Historik</h2>";
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_role = LARP_Role::loadByIds($role->Id, $prevoius_larp->Id);
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        echo "<strong>Intrig</strong><br>";
		        echo nl2br($previous_larp_role->Intrigue);
		        echo "<br><strong>Vad hände för $role->Name?</strong><br>";
		        if (isset($previous_larp_role->WhatHappened) && $previous_larp_role->WhatHappened != "")
		            echo $previous_larp_role->WhatHappened;
		            else echo "Inget rapporterat";
	            echo "<br><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "")
	                echo $previous_larp_role->WhatHappendToOthers;
	                else echo "Inget rapporterat";
	            echo "</div>";
		                
		    }
		}
			    
			
			
		?>
		

		

	</div>


</body>
</html>
