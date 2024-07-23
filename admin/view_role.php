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

/*
if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}
*/

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);

$isRegistered = $role->isRegistered($current_larp);


include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo $role->Name;?>&nbsp;
		<?php if ($role->IsDead ==1) echo "<i class='fa-solid fa-skull-crossbones' title='Död'></i>"?>

		<?php if ($isRegistered) {
			echo $role->getEditLinkPen(true);
		} ?>
		</h1>
		
		<?php if ($isRegistered) {?>	
		<a href='character_sheet.php?id=<?php echo $role->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för <?php echo $role->Name;?>'></i>Karaktärsblad för <?php echo $role->Name;?></a> &nbsp; 
		<a href='character_sheet.php?id=<?php echo $role->Id;?>&all_info=<?php echo date_format(new Datetime(),"suv") ?>' target='_blank'>
		<i class='fa-solid fa-file-pdf' title='All info om <?php echo $role->Name;?>'></i>All info om <?php echo $role->Name;?></a>
		<br><br>
		<?php } ?>
		<?php 
		if ($role->isApproved()) {
		  echo "<strong>Godkänd</strong>";
		  if (!empty($role->ApprovedByUserId) && !empty($role->ApprovedDate)) {
		      $approvedUser = User::loadById($role->ApprovedByUserId);
		      echo " av $approvedUser->Name, ".substr($role->ApprovedDate,0, 10); 
		  }
		  $editButton = "Ta bort godkännandet";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		    $editButton = "Godkänn";
		}		
		?>
        <form action="logic/toggle_approve_role.php" method="post"><input type="hidden" id="roleId" name="roleId" value="<?php echo $role->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
		<br>
		
            <?php 
            if ($isRegistered) {
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

		if ($isRegistered) {?>
    		<h2>Intrig <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
    		<div>
    		<?php    echo nl2br(htmlspecialchars($larp_role->Intrigue)); ?>
    		<?php 
    		if (!empty($larp_role->WhatHappened) || !empty($larp_role->WhatHappendToOthers)) {
    		    echo "<h3>Vad hände med/för $role->Name ?</h3>";
    		    echo  nl2br(htmlspecialchars($larp_role->WhatHappened));
    		    echo "<h3>Vad hände med/för andra?</h3>";
    		    echo  nl2br(htmlspecialchars($larp_role->WhatHappendToOthers));
    		}
    		    ?>
    		
    		<?php
    		$intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
    	    echo "<table class='data'>";
    	    echo "<tr><th>Intrig</th><th>Intrigtext</th><th></th></tr>";
            foreach ($intrigues as $intrigue) {
               echo "<tr>";
               echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrigspår: $intrigue->Number. $intrigue->Name</a>";
               if (!$intrigue->isActive()) echo " (inte aktuell)";
               echo "</td>";
               $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
               echo "<td>";
               if ($intrigue->isActive()) {
                   if (!empty($intrigue->CommonText)) echo nl2br(htmlspecialchars($intrigue->CommonText))."<br><br>";
                   echo nl2br(htmlspecialchars($intrigueActor->IntrigueText));
                   if (!empty($intrigueActor->OffInfo)) {
                       echo "<br><br><strong>Off-information:</strong><br>".nl2br(htmlspecialchars($intrigueActor->OffInfo));
                   }
                   if (!empty($intrigueActor->WhatHappened)) {
                       echo "<br><br><strong>Vad hände:</strong><br>".nl2br(htmlspecialchars($intrigueActor->WhatHappened));
                   }
                   echo "</td>";
                   echo "<td><a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$role->Name'><i class='fa-solid fa-pen'></i></a></td>";
                   echo "</tr>";
               }
               else {
                   if (!empty($intrigue->CommonText)) echo "<s>".nl2br(htmlspecialchars($intrigue->CommonText))."</s><br><br>";
                   echo "<s>$intrigueActor->IntrigueText</s>";
                   echo "</td>";
                   echo "</tr>";
               }
            }
           
           
           $known_groups = $role->getAllKnownGroups($current_larp);
           $known_roles = $role->getAllKnownRoles($current_larp);
           
           $known_npcgroups = $role->getAllKnownNPCGroups($current_larp);
           $known_npcs = $role->getAllKnownNPCs($current_larp);
           $known_props = $role->getAllKnownProps($current_larp);
           $known_pdfs = $role->getAllKnownPdfs($current_larp);	       
           
           $checkin_letters = $role->getAllCheckinLetters($current_larp);
           $checkin_telegrams = $role->getAllCheckinTelegrams($current_larp);
           $checkin_props = $role->getAllCheckinProps($current_larp);
           
           
           $subdivisions = Subdivision::allForRole($role);
           foreach ($subdivisions as $subdivision) {
               $subdivisionIntrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
               foreach ($subdivisionIntrigues as $intrigue) {
                   echo "<tr>";
                   echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrigspår: $intrigue->Number. $intrigue->Name</a>";
                   if (!$intrigue->isActive()) echo " (inte aktuell)";
                   echo "</td>";
                   $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
                   echo "<td>";
                   if ($intrigue->isActive()) {
                       if ($subdivision->isVisibleToParticipants()) echo "<strong>För ".$subdivision->getViewLink()."</strong><br>";
                       if (!empty($intrigue->CommonText)) echo nl2br(htmlspecialchars($intrigue->CommonText))."<br><br>";
                       echo nl2br(htmlspecialchars($intrigueActor->IntrigueText));
                       if (!empty($intrigueActor->OffInfo)) {
                           echo "<br><br><strong>Off-information:</strong><br>".nl2br(htmlspecialchars($intrigueActor->OffInfo));
                       }
                       if (!empty($intrigueActor->WhatHappened)) {
                           echo "<br><br><strong>Vad hände:</strong><br>".nl2br(htmlspecialchars($intrigueActor->WhatHappened));
                       }
                       echo "</td>";
                       echo "<td><a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$role->Name'><i class='fa-solid fa-pen'></i></a></td>";
                       echo "</tr>";
                   }
                   else {
                       if (!empty($intrigue->CommonText)) echo "<s>".nl2br(htmlspecialchars($intrigue->CommonText))."</s><br><br>";
                       echo "<s>$intrigueActor->IntrigueText</s>";
                       echo "</td>";
                       echo "</tr>";
                   }
               }
               
               echo "</table>";
               
               $known_groups = array_merge($known_groups,$subdivision->getAllKnownGroups($current_larp));
               $known_roles = array_merge($known_roles,$subdivision->getAllKnownRoles($current_larp));
               
               $known_npcgroups = array_merge($known_npcgroups,$subdivision->getAllKnownNPCGroups($current_larp));
               $known_npcs = array_merge($known_npcs,$subdivision->getAllKnownNPCs($current_larp));
               $known_props = array_merge($known_props,$subdivision->getAllKnownProps($current_larp));
               $known_pdfs = array_merge($known_pdfs,$subdivision->getAllKnownPdfs($current_larp));
               
               $checkin_letters = array_merge($checkin_letters,$subdivision->getAllCheckinLetters($current_larp));
               $checkin_telegrams = array_merge($checkin_telegrams,$subdivision->getAllCheckinTelegrams($current_larp));
               $checkin_props = array_merge($checkin_props,$subdivision->getAllCheckinProps($current_larp));
               
               
               
           }
           
           
           echo "<h3>Känner till</h3>";
           echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
           $temp=0;
           $cols=5;
           foreach ($known_groups as $known_group) {
               echo "<li style='display:table-cell; width:19%;'>";
               echo "<div class='name'>$known_group->Name</div>";
               echo "<div>Grupp</div>";
               if ($known_group->hasImage()) {
                   echo "<img src='../includes/display_image.php?id=$known_group->ImageId'/>\n";
               }
               echo "</li>";
               
               $temp++;
               if($temp==$cols)
               {
                   echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
                   $temp=0;
               }
           }
           foreach ($known_roles as $known_role) {
               echo "<li style='display:table-cell; width:19%;'>";
               echo "<div class='name'>$known_role->Name</div>";
               $role_group = $known_role->getGroup();
               if (!empty($role_group)) {
                   echo "<div>$role_group->Name</div>";
               }
               
               if ($known_role->hasImage()) {
                   echo "<img src='../includes/display_image.php?id=$known_role->ImageId'/>\n";
               }
               echo "</li>";
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
                   echo "<td>";
                   echo "<img width='100' src='../includes/display_image.php?id=$npc->ImageId'/>\n";
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
                   echo "<td>";
                   echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
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
           
           foreach ($known_pdfs as $known_pdf) {
               $intrigue_pdf = $known_pdf->getIntriguePDF();
    	       echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
    	       echo "<br>";
           }
           
           
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
                       echo "<td>";
                       echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
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
		<?php 
		if ($current_larp->hasRumours() && $isRegistered) {
		
		?>
		
		<h2>Rykten</h2>
		<div>
		<h3>Rykten som <?php echo $role->Name ?> känner till <a href='rumour_for.php?RoleId=<?php echo $role->Id ?>'><i class='fa-solid fa-plus' title='Tilldela rykten till <?php echo $role->Name ?>'></i></a></h3>
		<?php 
		$rumours = Rumour::allKnownByRole($current_larp, $role);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>	
		
		<h3>Rykten som handlar om <?php echo $role->Name ?> <a href='rumour_form.php?operation=insert&RoleId=<?php echo $role->Id ?>'><i class='fa-solid fa-plus' title='Skapa rykte om <?php echo $role->Name ?>'></i></a></h3>
		<?php 
		$rumours = Rumour::allConcernedByRole($current_larp, $role);
		foreach($rumours as $rumour) {
		    echo "$rumour->Text <a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></a><br>";
		}
		?>
		
		</div>
		<?php }?>
		
		<?php if ($isRegistered) {?>
		<h2>Handel</h2>
		<div>
		<?php 
		$currency = $current_larp->getCampaign()->Currency;
		$titledeeds = Titledeed::getAllForRole($role);
		foreach ($titledeeds as $titledeed) {
		    $numberOfOwners = $titledeed->numberOfOwners();
		    echo "<a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'>$titledeed->Name</a>";
		    if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
		    echo ", <a href='resource_titledeed_form.php?Id=$titledeed->Id'>Resultat ".$titledeed->calculateResult()." $currency</a>";
		    echo "<br>";
		    $produces_normally = $titledeed->ProducesNormally();
		    if (!empty($produces_normally)) echo "Tillgångar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
		    $requires_normally = $titledeed->RequiresNormally();
		    if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."<br>\n";
		    echo "<br>";
		    
		}
		if (isset($larp_role)) echo "Pengar vid lajvets start $larp_role->StartingMoney $currency";
		
		
		
		
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
		        if (!empty($previous_larp_role->Intrigue)) {
		            echo "<strong>Intrig</strong><br>";
		            echo "<p>".nl2br($previous_larp_role->Intrigue)."</p>";
		        }
		        
		        $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $prevoius_larp->Id);
		        foreach($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<div class='intrigue'>";
		                echo "<p><strong>Intrigspår: $intrigue->Name</strong><br>".nl2br($intrigueActor->IntrigueText)."</p>";
		                
		                echo "<p><strong>Vad hände med det?</strong><br>";
		                if (!empty($intrigueActor->WhatHappened)) echo nl2br($intrigueActor->WhatHappened);
		                else echo "Inget rapporterat";
		                echo "</p>";
		                echo "</div>";
		            }

		        }
		        
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
		    if (!empty($role->PreviousLarps)) {
		        echo "<div class='border'><h3>Tidigare</h3>";
		        echo "<p>".nl2br(htmlspecialchars($role->PreviousLarps))."</p></div>";
		    }
		    
		}
			    
			
			
		?>
		

	</div>


</body>
</html>
