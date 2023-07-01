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


if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}

$person = $role->getPerson();

include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $role->Name;?>&nbsp;
		<?php if ($role->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>
		
		<a href='edit_role.php?id=<?php echo $role->Id;?>'>
		<i class='fa-solid fa-pen'></i></a> 
		<a href='character_sheet.php?id=<?php echo $role->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för <?php echo $role->Name;?>'></i></a>
		</h1>
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
		<table>
				<?php 
				if ($role->isMysLajvare()) {
				    echo "<tr><td></td><td><strong>Myslajvare</strong></td></tr>";
				}?>
		
			<tr><td valign="top" class="header">Spelas av</td><td><a href ="view_person.php?id=<?php echo $role->PersonId;?>"><?php echo $person->Name; ?></a></td>
		<?php 
		if ($role->hasImage()) {
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<td rowspan='20' valign='top'><img width='300' src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/>";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</td>";
		}
		?>
			
			</tr>
		<?php if (isset($group)) {?>
			<tr><td valign="top" class="header">Grupp</td><td><a href ="view_group.php?id=<?php echo $group->Id;?>"><?php echo $group->Name; ?></a></td></tr>
		<?php }?>
		
		<?php if (!$isReserve) {?>
			<tr><td valign="top" class="header">Huvudkaraktär</td><td><?php echo ja_nej($larp_role->IsMainRole);?></td></tr>
		<?php }?>

			<tr><td valign="top" class="header">Yrke</td><td><?php echo $role->Profession;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br($role->Description);?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för gruppen</td><td><?php echo nl2br($role->DescriptionForGroup);?></td></tr>
			<tr><td valign="top" class="header">Beskrivning för andra</td><td><?php echo nl2br($role->DescriptionForOthers);?></td></tr>
		<?php if (!$role->isMysLajvare()) {?>
			<tr><td valign="top" class="header">Typ av lajvare</td><td>
			<?php 
			$larpertype = $role->getLarperType();
			if (!empty($larpertype)) echo $larpertype->Name;
			?>
			</td></tr>
			<tr><td valign="top" class="header">Kommentar till typ av lajvare</td><td><?php echo $role->TypeOfLarperComment;?></td></tr>
		
			<tr><td valign="top" class="header">Tidigare lajv</td><td><?php echo $role->PreviousLarps;?></td></tr>
			<tr><td valign="top" class="header">Varför befinner sig karaktären i Slow River?</td><td><?php echo $role->ReasonForBeingInSlowRiver;?></td></tr>
			<tr><td valign="top" class="header">Religion</td><td><?php echo $role->Religion;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet</td><td><?php echo $role->DarkSecret;?></td></tr>
			<tr><td valign="top" class="header">Mörk hemlighet - intrig idéer</td><td><?php echo nl2br($role->DarkSecretIntrigueIdeas); ?></td></tr>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($role->getIntrigueTypes());?></td></tr>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo nl2br($role->IntrigueSuggestions); ?></td></tr>
			<tr><td valign="top" class="header">Saker karaktären inte vill spela på</td><td><?php echo $role->NotAcceptableIntrigues;?></td></tr>
			<tr><td valign="top" class="header">Relationer med andra</td><td><?php echo $role->CharactersWithRelations;?></td></tr>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $role->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Rikedom</td><td>
			<?php 
			$wealth = $role->getWealth();
			if (!empty($wealth)) echo $wealth->Name;
			?>
			</td></tr>
			<tr><td valign="top" class="header">Var är karaktären född?</td><td><?php echo $role->Birthplace;?></td></tr>
			<tr><td valign="top" class="header">Var bor karaktären?</td><td>
			<?php 
			$por = $role->getPlaceOfResidence();
			if (!empty($por)) echo $por->Name;
			?>
			</td></tr>
		<?php }?>
			<tr><td valign="top" class="header">Död</td><td><?php echo ja_nej($role->IsDead);?></td></tr>

		</table>		
		</div>
		
		<?php 

		if (!$isReserve) {?>
		<h2>Intrig <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php    echo $larp_role->Intrigue; ?>
		<?php
		$known_actors = array();
		$known_npcs = array();
		$known_props = array();
		$intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
		if (!empty($intrigues)) {
		    echo "<table class='data'>";
		    echo "<tr><th>Intrig</th><th>Intrigtext</th></tr>";
	        foreach ($intrigues as $intrigue) {
	           echo "<tr>";
	           echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrig: $intrigue->Number. $intrigue->Name</a>";
	           if (!$intrigue->isActive()) echo " (inte aktuell)";
	           echo "</td>";
	           $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
	           echo "<td>";
	           if ($intrigue->isActive()) {
	               echo "$intrigueActor->IntrigueText";
	               if (!empty($intrigueActor->OffInfo)) {
	                   echo "<br><br><strong>Off-information:</strong><br>$intrigueActor->OffInfo";
	               }
	               echo "</td>";
	               echo "</tr>";
	               $known_actors = array_merge($known_actors, $intrigueActor->getAllKnownActors());
	               $known_npcs = array_merge($known_npcs, $intrigueActor->getAllKnownNPCs());
	               $known_props = array_merge($known_props, $intrigueActor->getAllKnownProps());
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
	               $group=$knownIntrigueActor->getGroup();
	               echo "<li style='display:table-cell; width:19%;'>";
	               echo "<div class='name'>$group->Name</div>";
	               echo "<div>Grupp</div>";
	               echo "</li>";
	               
	           } else {
	               $role = $knownIntrigueActor->getRole();
	               echo "<li style='display:table-cell; width:19%;'>";
	               echo "<div class='name'>$role->Name</div>";
	               $role_group = $role->getGroup();
	               if (!empty($role_group)) {
	                   echo "<div>$role_group->Name</div>";
	               }
	               
	               if ($role->hasImage()) {
	                   $image = Image::loadById($role->ImageId);
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
		}
			}
	    ?>
		</div>
		<h2>Anteckningar (visas inte för deltagaren)</h2>
		<div>
		<?php    echo $role->OrganizerNotes; ?>
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
