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


if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}
$isReserve = Reserve_LARP_Role::isReserve($role->Id, $current_larp->Id);

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);

$person = $role->getPerson();

include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $role->Name;?>&nbsp;
		<?php if ($role->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
		
		<a href='edit_role.php?id=<?php echo $role->Id;?>'>
		<i class='fa-solid fa-pen'></i></a> 
		</h1>
		<a href='character_sheet.php?id=<?php echo $role->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för <?php echo $role->Name;?>'></i>Karaktärsblad för <?php echo $role->Name;?></a> &nbsp; 
		<a href='character_sheet.php?id=<?php echo $role->Id;?>&all_info=<?php echo date_format(new Datetime(),"suv") ?>' target='_blank'>
		<i class='fa-solid fa-file-pdf' title='All info om <?php echo $role->Name;?>'></i>All info om <?php echo $role->Name;?></a>
		<br><br>
		<?php 
		if ($person->isApprovedCharacters($current_larp)) {
		  echo "<strong>Godkänd</strong>";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		}
		?>
		
            <?php 
            if (!$isReserve) {
                if ($larp_role->UserMayEdit  == 1) {
                    echo "Deltagaren får ändra karaktären " . showStatusIcon(false);
                    $editButton = "Ta bort tillåtelsen att ändra";
                }
                else {
                    
                    $editButton = "Tillåt deltagaren att ändra karaktären";
                }
            
                ?>
            <form action="logic/toggle_user_may_edit_role.php" method="post"><input type="hidden" id="roleId" name="roleId" value="<?php echo $role->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
            <?php }?>
		<div>
		<?php include 'print_role.php';?>
		</div>
		
		<?php 

		if (!$isReserve) {?>
		<h2>Intrig <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php    echo nl2br(htmlspecialchars($larp_role->Intrigue)); ?>
		<?php
		$known_actors = array();
		$known_npcs = array();
		$known_npcgroups = array();
		$known_props = array();
		$checkin_letters = array();
		$checkin_telegrams = array();
		$checkin_props = array();
		$intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
		if (!empty($intrigues)) {
		    echo "<table class='data'>";
		    echo "<tr><th>Intrig</th><th>Intrigtext</th><th></th></tr>";
	        foreach ($intrigues as $intrigue) {
	           echo "<tr>";
	           echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a>";
	           if (!$intrigue->isActive()) echo " (inte aktuell)";
	           echo "</td>";
	           $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
	           echo "<td>";
	           if ($intrigue->isActive()) {
	               echo nl2br(htmlspecialchars($intrigueActor->IntrigueText));
	               if (!empty($intrigueActor->OffInfo)) {
	                   echo "<br><br><strong>Off-information:</strong><br>".nl2br(htmlspecialchars($intrigueActor->OffInfo));
	               }
	               echo "</td>";
	               echo "<td><a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$role->Name'><i class='fa-solid fa-pen'></i></a></td>";
	               echo "</tr>";
	               $known_actors = array_merge($known_actors, $intrigueActor->getAllKnownActors());
	               $known_npcs = array_merge($known_npcs, $intrigueActor->getAllKnownNPCs());
	               $known_props = array_merge($known_props, $intrigueActor->getAllKnownProps());
	               $known_npcgroups = array_merge($known_npcgroups, $intrigueActor->getAllKnownNPCGroups());
	               $checkin_letters = array_merge($checkin_letters, $intrigueActor->getAllCheckinLetters());
	               $checkin_telegrams = array_merge($checkin_telegrams, $intrigueActor->getAllCheckinTelegrams());
	               $checkin_props = array_merge($checkin_props, $intrigueActor->getAllCheckinProps());
	           }
	           else {
	               echo "<s>$intrigueActor->IntrigueText</s>";
	               echo "</td>";
	               echo "</tr>";
	           }
	        }
	       echo "</table>";
	       echo "<h3>Känner till</h3>";
	       echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	       $temp=0;
	       $cols=5;
	       foreach ($known_actors as $known_actor) {
	           $knownIntrigueActor = $known_actor->getKnownIntrigueActor();
	           
	           if (!empty($knownIntrigueActor->GroupId)) {
	               $groupActor=$knownIntrigueActor->getGroup();
	               echo "<li style='display:table-cell; width:19%;'>";
	               echo "<div class='name'>$groupActor->Name</div>";
	               echo "<div>Grupp</div>";
	               echo "</li>";
	               
	           } else {
	               $roleActor = $knownIntrigueActor->getRole();
	               echo "<li style='display:table-cell; width:19%;'>";
	               echo "<div class='name'>$roleActor->Name</div>";
	               $role_group = $roleActor->getGroup();
	               if (!empty($role_group)) {
	                   echo "<div>$role_group->Name</div>";
	               }
	               
	               if ($roleActor->hasImage()) {
	                   $image = Image::loadById($roleActor->ImageId);
	                   if (!is_null($image)) {
	                       
	                       echo "<img src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
	                   }
	               }
	               echo "</li>";
	               
	           }
	           $temp++;
	           if($temp==$cols)
	           {
	               echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	               $temp=0;
	           }
	       }
	       foreach ($known_npcgroups as $known_npcgroup) {
	           $npcgroup=$known_npcgroup->getIntrigueNPCGroup()->getNPCGroup();
	           echo "<li style='display:table-cell; width:19%;'>\n";
	           echo "<div class='name'>$npcgroup->Name</div>\n";
	           echo "<div>NPC-grupp</div>";
	           echo "</li>\n";
	           $temp++;
	           if($temp==$cols)
	           {
	               echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	               $temp=0;
	           }
	       }
	       foreach ($known_npcs as $known_npc) {
	           $npc=$known_npc->getIntrigueNPC()->getNPC();
	           echo "<li style='display:table-cell; width:19%;'>\n";
	           echo "<div class='name'>$npc->Name</div>\n";
	           $npc_group = $npc->getNPCGroup();
	           if (!empty($npc_group)) {
	               echo "<div>$npc_group->Name</div>";
	           }
	           if ($npc->hasImage()) {
	               $image = Image::loadById($npc->ImageId);
	               echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
	           }
	           echo "</li>\n";
	           $temp++;
	           if($temp==$cols)
	           {
	               echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	               $temp=0;
	           }
	       }
	       foreach ($known_props as $known_prop) {
	           $prop = $known_prop->getIntrigueProp()->getProp();
               echo "<li style='display:table-cell; width:19%;'>\n";
               echo "<div class='name'>$prop->Name</div>\n";
               if ($prop->hasImage()) {
                   $image = Image::loadById($prop->ImageId);
                   echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
               }
               echo "</li>\n";
               $temp++;
               if($temp==$cols)
               {
                   echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
                   $temp=0;
               }
	       }
	       echo "</ul>";
	       
	       
	       
	       if (!empty($checkin_letters) || !empty($checkin_telegrams) || !empty($checkin_props)) {
	           echo "<h3>Ska ha vid incheckning</h3>";
	           foreach ($checkin_letters as $checkin_letter) {
	               $letter = $checkin_letter->getIntrigueLetter()->getLetter();
	               echo "Brev från: $letter->Signature till: $letter->Recipient, ".mb_strimwidth(str_replace('\n', '<br>', $letter->Message), 0, 50, '...');
	               echo "<br>";
	           }
	           foreach ($checkin_telegrams as $checkin_telegram) {
	               $telegram=$checkin_telegram->getIntrigueTelegram()->getTelegram();
	               echo "Telegram från: $telegram->Sender till: $telegram->Reciever, ".mb_strimwidth(str_replace('\n', '<br>', $telegram->Message), 0, 50, '...');
	               echo "<br>";
	           }
	           echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	           $temp=0;
	           $cols=5;
	           foreach ($checkin_props as $checkin_prop) {
	               $prop=$checkin_prop->getIntrigueProp()->getProp();
	               echo "<li style='display:table-cell; width:19%;'>\n";
	               echo "<div class='name'>$prop->Name</div>\n";
	               if ($prop->hasImage()) {
	                   $image = Image::loadById($prop->ImageId);
	                   echo "<td><img width=100 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>\n";
	               }
	               echo "</li>\n";
	               $temp++;
	               if($temp==$cols)
	               {
	                   echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
	                   $temp=0;
	               }
	           }
	           echo "</ul>";
	       }
	       
	       
	       
		}
			}
	    ?>
		</div>
		<h2>Rykten</h2>
		<div>
		<h3>Rykten som <?php echo $role->Name ?> känner till</h3>
		<?php 
		$rumours = Rumour::allKnownByRole($current_larp, $role);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>	
		
		<h3>Rykten som handlar om <?php echo $role->Name ?> <a href='rumour_form.php?operation=new&RoleId=<?php echo $role->Id ?>'><i class='fa-solid fa-plus' title='Skapa rykte om <?php echo $role->Name ?>'></i></a></h3>
		<?php 
		$rumours = Rumour::allConcernedByRole($current_larp, $role);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>
		
		</div>
		<h2>Anteckningar (visas inte för deltagaren) <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php    echo nl2br(htmlspecialchars($role->OrganizerNotes)); ?>
		</div>
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
		            echo nl2br(htmlspecialchars($previous_larp_role->WhatHappened));
		            else echo "Inget rapporterat";
	            echo "<br><strong>Vad hände för andra?</strong><br>";
	            if (isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "")
	                echo nl2br(htmlspecialchars($previous_larp_role->WhatHappendToOthers));
	                else echo "Inget rapporterat";
	            echo "</div>";
		                
		    }
		}
			    
			
			
		?>
		

	</div>


</body>
</html>
