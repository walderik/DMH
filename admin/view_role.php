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
$reserve_larp_role = Reserve_LARP_Role::loadByIds($role->Id, $current_larp->Id);
$assignment = NPC_assignment::getAssignment($role, $current_larp);
if (isset($reserve_larp_role)) $isReserve = true;
else $isReserve = false;
if (isset($assignment)) $isPlayedNPC = true;
else $isPlayedNPC = false;

$isRegistered = $role->isRegistered($current_larp);
$subdivisions = Subdivision::allForRole($role, $current_larp);

include 'navigation.php';
include 'aktor_navigation.php';
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
		  if (!empty($role->ApprovedByPersonId) && !empty($role->ApprovedDate)) {
		      $approver = Person::loadById($role->ApprovedByPersonId);
		      echo " av $approver->Name, ".substr($role->ApprovedDate,0, 10); 
		  }
		  $editButton = "Ta bort godkännandet";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		    $editButton = "Godkänn";
		}		
		if ($role->userMayEdit()) {
		    echo "<br>Spelaren får ändra på karaktären och därför kan den varken godkännas eller underkännas.";
		} else { 
		     echo "<form action='logic/toggle_approve_role.php' method='post'><input type='hidden' id='roleId' name='roleId' value='$role->Id'><input type='submit' value='$editButton'></form>";
		} 
		
		?>
		<br>
		
            <?php 
            if ($isRegistered && !$isReserve) {
                if ($role->userMayEdit()) {
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

		if ($isRegistered && !$isReserve || $isPlayedNPC) {?>
		
			<?php if ($isPlayedNPC) { ?>
			<h2>Intrig</h2>
    		<div>
	    		<i>Kursiverad text visas inte för deltagaren.</i><br><br>
    		<?php 
        		if (!empty($assignment->WhatHappened) || !empty($assignment->WhatHappendToOthers)) {
        		    echo "<h3>Vad hände med/för $role->Name ?</h3>";
        		    echo  nl2br(htmlspecialchars($assignment->WhatHappened));
        		    echo "<h3>Vad hände med/för andra?</h3>";
        		    echo  nl2br(htmlspecialchars($assignment->WhatHappendToOthers));
        		}
		      } else {?>
    		<h2>Intrig <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
    		<div>
    		    		<i>Kursiverad text visas inte för deltagaren.</i><br><br>
    		<?php    echo nl2br(htmlspecialchars($larp_role->Intrigue)); ?>
    		<?php 
    		if (!empty($larp_role->WhatHappened) || !empty($larp_role->WhatHappendToOthers) || !empty($larp_role->WhatHappensAfterLarp)) {
    		    echo "<h3>Vad hände med/för $role->Name ?</h3>";
    		    echo  nl2br(htmlspecialchars($larp_role->WhatHappened));
    		    echo "<h3>Vad hände med/för andra?</h3>";
    		    echo  nl2br(htmlspecialchars($larp_role->WhatHappendToOthers));
    		    echo "<h3>Vad händer fram till nästa lajv?</h3>";
    		    echo  nl2br(htmlspecialchars($larp_role->WhatHappensAfterLarp));
    		}
    		    ?>

			<?php } ?>
    		<?php
    		$intrigues = array();
    		$roleIntrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
    		
    		$intrigues = array_merge($intrigues, $roleIntrigues);
    		$subdivisions = Subdivision::allForRole($role, $current_larp);
    		foreach ($subdivisions as $subdivision) {
    		    $intrigues = array_merge($intrigues, Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id));
    		}
    		$intrigues = array_unique($intrigues, SORT_REGULAR);
    		usort($intrigues, function ($a, $b) {
    		    $a_val =  $a->Number;
    		    $b_val =  $b->Number;
    		    
    		    if($a_val > $b_val) return 1;
    		    if($a_val < $b_val) return -1;
    		    return 0;
    		});
    		    
		    echo "<table class='data'>";
		    foreach ($intrigues as $intrigue) {
		        if ($intrigues[0]->isActive()) {
		            $txt = "";
		            //Hitta alla intrigueActors som är aktuella, se att karaktären kommer först om den finns med
		            $intrigueActors = array();
		            $roleActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
		            if (!empty($roleActor)) $intrigueActors[] = $roleActor;
		            $invisibleSubdivisionActors = array();
		            $visibleSubdivisionActors = array();
		            foreach ($subdivisions as $subdivision) {
		                $subdivisionActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
		                if (!empty($subdivisionActor)) {
		                    if ($subdivision->isVisibleToParticipants()) $visibleSubdivisionActors[] = $subdivisionActor;
		                    else $invisibleSubdivisionActors[] = $subdivisionActor;
		                }
		            }
		            if (!empty($invisibleSubdivisionActors)) $intrigueActors = array_merge($intrigueActors, $invisibleSubdivisionActors);
		            if (!empty($visibleSubdivisionActors)) $intrigueActors = array_merge($intrigueActors, $visibleSubdivisionActors);
		            
		            
		            
		            //Om det bara är en intrigaktör och det är en synlig gruppering ska även För <namn> skrivas ut
		            $singleVisibleSubdivisionActor = false;
		            
		            if (!empty($intrigue->CommonText)) {
		                if (sizeOf($intrigueActors) == 1 && $intrigueActors[0]->isSubdivisionActor()) {
		                    $subdivision = $intrigueActors[0]->getSubdivision();
		                    if ($subdivision->isVisibleToParticipants()) {
		                        $txt .= "<p><i>Gemensam text:</i><br><strong>$subdivision->Name</strong><br>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		                        $singleVisibleSubdivisionActor = true;
		                    }
		                    else $txt .= "<p><i>Gemensam text:</i><br>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		                } else $txt .= "<p><i>Gemensam text:</i><br>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		            }
		            
		            $intrigueTextArr = array();
		            foreach ($intrigueActors as $intrigueActor) {
		                if (!empty($intrigueActor->IntrigueText) && !in_array($intrigueActor->IntrigueText, $intrigueTextArr)) {
		                    
		                    $intrigueText = "";
		                    if ($intrigueActor->isSubdivisionActor())  {
		                        $subdivision = $intrigueActor->getSubdivision();
		                        
		                        if ($subdivision->isVisibleToParticipants()) $intrigueText .= "<strong>$subdivision->Name</strong>";
		                        else $intrigueText .= "<i>$subdivision->Name</i>";
		                        $intrigueText .= " <a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$subdivision->Name'><i class='fa-solid fa-pen'></i></a>";
		                        
		                        
		                    } else {
		                        $intrigueText = "<i>Personlig intrigtext</i>";
		                        $intrigueText .= " <a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$role->Name'><i class='fa-solid fa-pen'></i></a>";
		                    }
		                    $intrigueText .= "<br>".nl2br(htmlspecialchars($intrigueActor->IntrigueText));
		                    $intrigueTextArr[] =  $intrigueText;
		                }
		            }
		            if (!empty($intrigueTextArr)) {
		                $txt .=  "<p>".join("<br><br>",$intrigueTextArr). "</p>";
		            }
		            
		            $offTextArr = array();
		            foreach ($intrigueActors as $intrigueActor) {
		                if (!empty($intrigueActor->OffInfo)  && !in_array($intrigueActor->OffInfo, $offTextArr)) {
		                    $offTextArr[] =  nl2br(htmlspecialchars($intrigueActor->OffInfo));
		                }
		            }
		            if (!empty($offTextArr)) {
		                $txt .= "<p><strong>Off-information:</strong><br><i>".join("<br><br>",$offTextArr)."</i></p>";
		            }
		            
		            if (!empty($txt)) {
		                echo "<tr><td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrigspår $intrigue->Number<br>$intrigue->Name</a></td><td>".$txt."</td><tr>";
		            }
		            
		        }
		    }
		    echo "</table>";
    		
    		
    		
    		
           $known_groups = $role->getAllKnownGroups($current_larp);
           $known_roles = $role->getAllKnownRoles($current_larp);
           
           $known_props = $role->getAllKnownProps($current_larp);
           $known_pdfs = $role->getAllKnownPdfs($current_larp);	       
           
           $checkin_letters = $role->getAllCheckinLetters($current_larp);
           $checkin_telegrams = $role->getAllCheckinTelegrams($current_larp);
           $checkin_props = $role->getAllCheckinProps($current_larp);
           
           foreach ($subdivisions as $subdivision) {
               $known_groups = array_unique(array_merge($known_groups,$subdivision->getAllKnownGroups($current_larp)), SORT_REGULAR);
               $known_roles = array_unique(array_merge($known_roles,$subdivision->getAllKnownRoles($current_larp)), SORT_REGULAR);
               
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
               if ($known_role->isPC($current_larp) && !$known_role->isRegistered($current_larp)) echo "Inte anmäld";
               elseif ($known_role->isNPC($current_larp)) {
                   $assignment = NPC_assignment::getAssignment($known_role, $current_larp);
                   if (!empty($assignment)) {
                       $person = $assignment->getPerson();
                       if (!empty($person)) echo "<div>NPC - Spelas av $person->Name</div>";
                       else echo "<div>NPC - Spelare inte tilldelad ännu</div>";
                   } else {
                       echo "NPC - Spelas inte";
                   }
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
           ?>
		</div>
		<?php }?>
		
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
		
		<?php if ($isRegistered && !$isReserve) {?>
		<h2>Handel</h2>
		<div>
		<?php 
		$currency = $current_larp->getCampaign()->Currency;
		$titledeeds = Titledeed::getAllForRole($role);
		foreach ($titledeeds as $titledeed) {
		    echo "<a href='view_titledeed.php?id=$titledeed->Id'>$titledeed->Name</a>";
		    if (!$titledeed->isGeneric()) {
	           $numberOfOwners = $titledeed->numberOfOwners();
	           if ($numberOfOwners > 1) echo " 1/$numberOfOwners";
		    }
		    if ($titledeed->isInUse()) echo ", <a href='resource_titledeed_form.php?Id=$titledeed->Id'>Resultat ".$titledeed->calculateResult()." $currency</a>";
		    else echo ", ej i spel";
		    echo "<br>";
		    $produces_normally = $titledeed->ProducesNormally();
		    if (!empty($produces_normally)) echo "Tillgångar: ". commaStringFromArrayObject($produces_normally) . "<br>\n";
		    $requires_normally = $titledeed->RequiresNormally();
		    if (!empty($requires_normally)) echo "Behöver: " . commaStringFromArrayObject($requires_normally)."<br>\n";
		    echo "<br>";
		    
		}
		if (isset($larp_role)) echo "Pengar vid lajvets start $larp_role->StartingMoney $currency";
		?>
		
		</div>
		<?php }?>
		

		
		<h2>Anteckningar (visas inte för deltagaren) <a href='edit_intrigue.php?id=<?php echo $role->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php    echo nl2br(htmlspecialchars($role->OrganizerNotes)); ?>
		</div>

		<?php include 'print_role_history.php';?>		

	</div>


</body>
</html>
