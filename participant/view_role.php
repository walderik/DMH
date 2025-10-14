<?php

require 'header.php';

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
if ($role->isPC()) $person = $role->getPerson();

if ($role->isPC() && $person->Id != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

$group = $role->getGroup();

if ($role->isNPC()) {
    $assignment = NPC_assignment::getAssignment($role, $current_larp);
    if (!empty($assignment) && ($assignment->PersonId == $current_person->Id)) {
        //Ok, du är tilldelad NPC'n
    } elseif (!empty($group) && $current_person->isMemberGroup($group)) {
        //Ok, din grupp
    } else {
        header('Location: index.php'); //NPC som inte är din och inte är med i din grupp
        exit;
    }
}

$larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);

if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}


$isMob = is_numeric(strpos(strtolower($_SERVER["HTTP_USER_AGENT"]), "mobile"));

if($isMob){
    $columns=2;
    $type="Mobile";
    //echo 'Using Mobile Device...';
}else{
    $columns=5;
    $type="Computer";
    //echo 'Using Desktop...';
}
$temp=0;


function printIntrigue($number, $commonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, $alwaysPrintWhatHappened) {
    $formattedText = "";
    if (!empty($commonText)) {
        $formattedText .= "<p>";
        if (!empty($commonTextHeader)) {
            $formattedText .= "<strong>".htmlspecialchars($commonTextHeader)."</strong><br>";
        }
        $formattedText .= nl2br(htmlspecialchars($commonText))."</p>";
    }
    
    if (!empty($intrigueTextArr)) {
        $tmpIntrigueTextArr = array();
        foreach ($intrigueTextArr as $intrigueText) {
            if (is_array($intrigueText)) {
                $tmpIntrigueTextArr[] = "<strong>".htmlspecialchars($intrigueText[0])."</strong><br>".nl2br(htmlspecialchars($intrigueText[1]));
            } else {
                $tmpIntrigueTextArr[] = nl2br(htmlspecialchars($intrigueText));
            } 
        }
        $formattedText .= "<p>".join("<br><br>",$tmpIntrigueTextArr). "</p>";

    
    
    }
    
    if (!empty($offTextArr)) {
        $tmpOffTextArr = array();
        foreach ($offTextArr as $offText) {
            $tmpOffTextArr[] = nl2br(htmlspecialchars($offText));
        }
        $formattedText .= "<p><strong>Off-information:</strong><br><i>".join("<br><br>",$tmpOffTextArr)."</i></p>";
    }

    if (!empty($whatHappenedTextArr) || $alwaysPrintWhatHappened) {
        $tmpWhatHappenedTextArr = array();
        foreach ($whatHappenedTextArr as $whatHappenedText) {
            if (is_array($whatHappenedText)) {
                $tmpWhatHappenedTextArr[] = "<strong>".htmlspecialchars($whatHappenedText[0])."</strong><br>".nl2br(htmlspecialchars($whatHappenedText[1]))."<br><br>";
            } else {
                $tmpWhatHappenedTextArr[] = nl2br(htmlspecialchars($whatHappenedText));
            }
        }
        $formattedText .= "<p><strong>Vad hände med det:</strong><br>";
        if (!empty($tmpWhatHappenedTextArr)) $formattedText .= join("<br><br>",$tmpWhatHappenedTextArr);
        else $formattedText .= "Inget rapporterat";
        $formattedText .= "</p>";
    }
    
    if (!empty($formattedText)) {
        echo "<h3>Intrig $number:</h3>".$formattedText;
    }
    
}


include 'navigation.php';
?>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-person"></i>
			<?php 
			echo $role->Name;
    		//Karaktärsblad
    		if ($role->isPC()) echo " <a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
    		if ($role->userMayEdit()) echo " " . $role->getEditLinkPen(false);

    		?>
		</div>


		<?php 
		if ($role->hasImage()) {
		    echo "<div class='itemcontainer'>";
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<img width='300' src='../includes/display_image.php?id=$role->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    if ($role->userMayEdit()) echo "<br><a href='../common/logic/rotate_image.php?id=$role->ImageId'><i class='fa-solid fa-rotate-right'></i></a> <a href='logic/delete_image.php?id=$role->Id&type=role'><i class='fa-solid fa-trash'></i></a></td>\n";
		    echo "</div>";
		} elseif ($role->isNPC() && $role->userMayEdit()) {
		    echo "<div class='itemcontainer'>";
		    echo "<a href='upload_image.php?id=$role->Id&type=role'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a> \n";
		    echo "</div>";
		}
		?>

   		<div class='itemcontainer'>
       	<div class='itemname'>Godkänd</div>
		<?php echo ja_nej($role->isApproved());?>
		</div>
	
		<?php if ($role->isPC()) {?>
   		<div class='itemcontainer'>
       	<div class='itemname'>Spelas av</div>
		<?php echo $person->Name;?>
		</div>
		<?php }?>
			

		<?php if (isset($group)) {?>
			   <div class='itemcontainer'>
               <div class='itemname'>Grupp</div>
		
				<?php echo $group->getViewLink() ?>
				</div>
		<?php }?>

	   <div class='itemcontainer'>
       <div class='itemname'>Yrke</div>
		<?php echo $role->Profession;?>
		</div>
		
		<?php if ($role->isPC()) { ?>
	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($role->Description));?>
	   </div>
	   <?php } ?>

	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning för gruppen</div>
		<?php echo nl2br(htmlspecialchars($role->DescriptionForGroup));?>
		</div>
		
		<?php if ($role->isPC() ||  !empty($role->DescriptionForOthers)) { ?>
	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning för andra</div>
	   <?php echo nl2br(htmlspecialchars($role->DescriptionForOthers));?>
	   </div>
		<?php }?>

		<?php if (Race::isInUse($current_larp)) {?>
		   <div class='itemcontainer'>
           <div class='itemname'>Ras</div>
			<?php 
			$race = $role->getRace();
			if (!empty($race)) echo $race->Name;
			?>			   
			</div>
		
			<?php if (!empty($role->RaceComment)) {?>
		   <div class='itemcontainer'>
           <div class='itemname'>Kommentar till ras</div>
		   <?php echo $role->RaceComment;?>
		   </div>
		   <?php } ?>
		<?php } ?>

		<?php if (!$role->isMysLajvare()) {?>
		
			<?php if (LarperType::isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Typ av lajvare</div>
    			<?php 
    			$larpertype = $role->getLarperType();
    			if (!empty($larpertype)) echo $larpertype->Name;
    			?>			   
				</div>
			
				<?php if (!empty($role->TypeOfLarperComment)) {?>
			   <div class='itemcontainer'>
               <div class='itemname'>Kommentar till typ av lajvare</div>
			   <?php echo $role->TypeOfLarperComment;?>
			   </div>
			   <?php } ?>
			<?php } ?>

		  <?php if ($role->isPC() ||  !empty($role->ReasonForBeingInSlowRiver)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Varför befinner sig karaktären på platsen?</div>
		   <?php echo nl2br($role->ReasonForBeingInSlowRiver);?>
		   </div>
		   <?php } ?>
			
			<?php if (RoleFunction:: isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Funktioner</div>
       			<?php 
       			$functions = $role->getRoleFunctions();
               	if (empty($functions)) echo "Inga";
               	else echo commaStringFromArrayObject($functions);
               	?>	   
				</div>
			
				<?php if (!empty($role->RoleFunctionComment)) {?>
			   <div class='itemcontainer'>
               <div class='itemname'>Funktioner förklaring</div>
			   <?php echo $role->RoleFunctionComment;?>
			   </div>
			   <?php } ?>
			<?php }?>
			
			<?php if (Ability::isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Kunskaper</div>
               	<?php 
               	$abilities = $role->getAbilities();
               	if (empty($abilities)) echo "Inga";
               	else echo commaStringFromArrayObject($abilities);
               	?>		   
				</div>
			
				<?php if (!empty($role->AbilityComment)) {?>
			   <div class='itemcontainer'>
               <div class='itemname'>Kunskaper förklaring</div>
			   <?php echo $role->AbilityComment;?>
			   </div>
			   <?php } ?>
			<?php }?>			
			
			<?php if (Religion::isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Religion</div>
    			<?php 
    			$religion = $role->getReligion();
    			if (!empty($religion)) echo $religion->Name;
    			?>			   
				</div>
			
				<?php if (!empty($role->Religion)) {?>
			   <div class='itemcontainer'>
               <div class='itemname'>Religion förklaring</div>
			   <?php echo $role->Religion;?>
			   </div>
			   <?php } ?>
			<?php }?>

			<?php if ($role->isPC() ||  !empty($role->DarkSecret)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Mörk hemlighet</div>
		   <?php echo $role->DarkSecret;?>
		   </div>
		   <?php } ?>

			<?php if ($role->isPC() ||  !empty($role->DarkSecretIntrigueIdeas)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Mörk hemlighet - intrig idéer</div>
		   <?php echo nl2br(htmlspecialchars($role->DarkSecretIntrigueIdeas));?>
		   </div>
		   <?php } ?>
			
			
			
			<?php if ($role->isPC()) { ?>	
				<?php if (IntrigueType::isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Intrigtyper</div>
    			<?php echo commaStringFromArrayObject($role->getIntrigueTypes());?>		   
				</div>
				<?php }?>


		   <div class='itemcontainer'>
           <div class='itemname'>Intrigidéer</div>
		   <?php echo nl2br(htmlspecialchars($role->IntrigueSuggestions));?>
		   </div>
		   <?php } ?>

		   <?php if ($role->isPC() ||  !empty($role->NotAcceptableIntrigues)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Saker karaktären inte vill spela på</div>
		   <?php echo nl2br(htmlspecialchars($role->NotAcceptableIntrigues));?>
		   </div>
		   <?php } ?>

			<?php if ($role->isPC() ||  !empty($role->CharactersWithRelations)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Relationer med andra</div>
		   <?php echo nl2br(htmlspecialchars($role->CharactersWithRelations));?>
		   </div>
		   <?php } ?>

			<?php if (Wealth::isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Religion</div>
    			<?php 
    			$wealth = $role->getWealth();
    			if (!empty($wealth)) echo $wealth->Name;
    			?>			   
				</div>
			<?php } ?>
			
		   <?php if ($role->isPC() ||  !empty($role->Birthplace)) { ?>	
		   <div class='itemcontainer'>
           <div class='itemname'>Var är karaktären född?</div>
		   <?php echo $role->Birthplace;?>
		   </div>
		   <?php } ?>
			
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
				<div class='itemcontainer'>
               	<div class='itemname'>Var bor karaktären?</div>
    			<?php 
    			$por = $role->getPlaceOfResidence();
    			if (!empty($por)) echo $por->Name;
    			?>			   
				</div>
			<?php } ?>

			<?php if (!empty($role->RoleFunctionComment)) {?>
		   <div class='itemcontainer'>
           <div class='itemname'>Annan information</div>
		   <?php echo nl2br(htmlspecialchars($role->OtherInformation));?>
		   </div>
		   <?php } ?>

		<?php }?>
		</div>
		
		<?php 
		if (isset($larp_role) || isset($assignment)) {
		?>
		<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Intrig
		</div>
			<div class='itemcontainer'>
			<?php if ($current_larp->isIntriguesReleased()) {
			    if (!empty($larp_role) && !empty($larp_role->Intrigue)) echo "<p>".nl2br(htmlspecialchars($larp_role->Intrigue)) ."</p>"; 
			    
			    $intrigues = $role->getAllIntriguesIncludingSubdivisionsSorted($current_larp);
			    $subdivisions = Subdivision::allForRole($role, $current_larp);
			    
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {		    

		                
		                $commonTextHeader = "";
		                $intrigueTextArr = array();
		                $offTextArr = array();
		                $whatHappenedTextArr = array();
		                
		                $intrigue->findAllInfoForRoleInIntrigue($role, $subdivisions, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr);
		                
                       
		                printIntrigue($intrigue->Number, $intrigue->CommonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, false);
		                
		                
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
		        
		        

		        
		        foreach ($subdivisions as $subdivision) {
    		        $known_groups = array_unique(array_merge($known_groups,$subdivision->getAllKnownGroups($current_larp)), SORT_REGULAR);
    		        $known_roles = array_unique(array_merge($known_roles,$subdivision->getAllKnownRoles($current_larp)), SORT_REGULAR);
    		        
    		        $known_npcgroups = array_merge($known_npcgroups,$subdivision->getAllKnownNPCGroups($current_larp));
    		        $known_npcs = array_merge($known_npcs,$subdivision->getAllKnownNPCs($current_larp));
    		        $known_props = array_merge($known_props,$subdivision->getAllKnownProps($current_larp));
    		        $known_pdfs = array_merge($known_pdfs,$subdivision->getAllKnownPdfs($current_larp));
    		        
    		        $checkin_letters = array_merge($checkin_letters,$subdivision->getAllCheckinLetters($current_larp));
    		        $checkin_telegrams = array_merge($checkin_telegrams,$subdivision->getAllCheckinTelegrams($current_larp));
    		        $checkin_props = array_merge($checkin_props,$subdivision->getAllCheckinProps($current_larp));
		        }
		        
                if (!empty($known_groups) || !empty($known_roles) || !empty($known_npcs) || !empty($known_props) || !empty($known_npcgroups)) {
			        echo "<h3>Känner till</h3>";
			        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			        foreach ($known_groups as $known_group) {
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'><a href='view_known_group.php?id=$known_group->Id'>$known_group->Name</a></div>";
			            echo "<div>Grupp</div>";
			            if ($known_group->hasImage()) {
			                echo "<img src='../includes/display_image.php?id=$known_group->ImageId'/>\n";
			            }
			            echo "</li>";
			            
			            $temp++;
			            if($temp==$columns)
			            {
			                echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			                $temp=0;
			            }
			        }
			        foreach ($known_roles as $known_role) {
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'><a href='view_known_role.php?id=$known_role->Id'>$known_role->Name</a></div>";
			            $role_group = $known_role->getGroup();
			            if (!empty($role_group)) {
			                echo "<div>$role_group->Name</div>";
			            }
			            
			            if ($known_role->hasImage()) {
			                echo "<img src='../includes/display_image.php?id=$known_role->ImageId'/>\n";
			            }
			            echo "</li>";
			            $temp++;
			            if($temp==$columns)
			            {
			                echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			                $temp=0;
			            }
			        }
			        
			        foreach ($known_npcgroups as $known_npcgroup) {
			            $npcgroup=$known_npcgroup->getIntrigueNPCGroup()->getNPCGroup();
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'>$npcgroup->Name</div>\n";
			            echo "<div>NPC-grupp</div>";
			            echo "</li>\n";
			            $temp++;
			            if($temp==$columns)
			            {
			                echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			                $temp=0;
			            }
			        }
			        foreach ($known_npcs as $known_npc) {
			            $npc=$known_npc->getIntrigueNPC()->getNPC();
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
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
			            if($temp==$columns)
			            {
			                echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			                $temp=0;
			            }
			        }
			        foreach ($known_props as $known_prop) {
			            $prop = $known_prop->getIntrigueProp()->getProp();
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'>$prop->Name</div>\n";
			            if ($prop->hasImage()) {
			                echo "<td>";
			                echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
			            }
			            echo "</li>\n";
			            $temp++;
			            if($temp==$columns)
			            {
			                echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			                $temp=0;
			            }
			        }
			        echo "</ul>";
		        }

		        foreach ($known_pdfs as $known_pdf) {
		            $intrigue_pdf = $known_pdf->getIntriguePDF();
		            echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
		            echo "<br>";
		        }
		        
		        
		        if (!empty($checkin_letters) || !empty($checkin_telegrams) || !empty($checkin_props)) {
		            echo "<h3>Ska ha vid incheckning</h3>";
		            foreach ($checkin_letters as $checkin_letter) {
		                $letter = $checkin_letter->getIntrigueLetter()->getLetter();
		                echo "Brev från: $letter->Signature till: $letter->Recipient<br>";
		            }
		            foreach ($checkin_telegrams as $checkin_telegram) {
		                $telegram=$checkin_telegram->getIntrigueTelegram()->getTelegram();
		                echo "Telegram från: $telegram->Sender till: $telegram->Reciever<br>";
		            }
		            echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		            foreach ($checkin_props as $checkin_prop) {
		                $prop=$checkin_prop->getIntrigueProp()->getProp();
		                if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
		                else echo "<li style='display:table-cell; width:49%;'>\n";
		                echo "<div class='name'>$prop->Name</div>\n";
		                if ($prop->hasImage()) {
		                    echo "<td>";
		                    echo "<img width='100' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
		                }
		                echo "</li>\n";
		                $temp++;
		                if($temp==$columns)
		                {
		                    echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		                    $temp=0;
		                }
		            }
		            echo "</ul>";
		        }
		        
		        $rumours = Rumour::allKnownByRole($current_larp, $role);
		        if (!empty($rumours)) {
    		        echo "<h2>Rykten</h2>";
            		echo "<ul style='list-style-type: disc;'>";
            		foreach($rumours as $rumour) {
            		    echo "<li style='margin-bottom:7px;margin-left:20px'>$rumour->Text\n";
    
            		}
            		echo "</ul>";
		        }
			}

			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
			</div>
		</div>
		<?php 
		}
		?>
		
		
		<?php 
		$previous_larps = $role->getPreviousLarps($current_larp);
		if (isset($previous_larps) && count($previous_larps) > 0 || !empty($role->PreviousLarps)) { 
		    ?>
		    
		    <div class='itemselector'>
		    <div class="header">
		    
		    <i class="fa-solid fa-landmark"></i> Historik
		    </div>
		    <div class='itemcontainer' style='padding-top:0px;'>
		    
		   <?php 
		    foreach ($previous_larps as $prevoius_larp) {
		        $previous_larp_role = LARP_Role::loadByIds($role->Id, $prevoius_larp->Id);
		        echo "<h2 style='border-top:none;'>$prevoius_larp->Name</h2>";
		        echo "<div class='border'>";
		        
		        if (isset($previous_larp_role) && !empty($previous_larp_role->Intrigue)) {
		            echo "<p>".nl2br(htmlspecialchars($previous_larp_role->Intrigue))."</p>";
		        }
		        
		        $intrigues = $role->getAllIntriguesIncludingSubdivisionsSorted($prevoius_larp);
		        $subdivisions = Subdivision::allForRole($role, $prevoius_larp);
		        
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                		                
		                $commonTextHeader = "";
		                $intrigueTextArr = array();
		                $offTextArr = array();
		                $whatHappenedTextArr = array();
		                
		                $intrigue->findAllInfoForRoleInIntrigue($role, $subdivisions, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr);
		                //Visa alltid rubriken "Vad hände"
		                printIntrigue($intrigue->Number, $intrigue->CommonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, true);
		                
		                
		            }
		        }
		        echo "<h3>Allmänna kommentarer</h3>";
		        
		        $previous_assignment = NPC_assignment::getAssignment($role, $prevoius_larp);
		        if (isset($previous_larp_role)) {
    		        echo "<p><strong>Vad hände för $role->Name?</strong><br>";
    		        if (isset($previous_larp_role->WhatHappened) && $previous_larp_role->WhatHappened != "")
    		            echo nl2br(htmlspecialchars($previous_larp_role->WhatHappened));
    	            else echo "Inget rapporterat";
    	            echo "</p>";
    	            echo "<p><strong>Vad hände för andra?</strong><br>";
    	            if (isset($previous_larp_role->WhatHappendToOthers) && $previous_larp_role->WhatHappendToOthers != "")
    	                echo nl2br(htmlspecialchars($previous_larp_role->WhatHappendToOthers));
                    else echo "Inget rapporterat";
                    echo "</p>";
                    echo "<p><strong>Vad händer efter lajvet?</strong><br>";
                    if (isset($previous_larp_role->WhatHappensAfterLarp) && $previous_larp_role->WhatHappensAfterLarp != "")
                        echo nl2br(htmlspecialchars($previous_larp_role->WhatHappensAfterLarp));
                    else echo "Inget rapporterat";
                    echo "</p>";
                    echo "</div>";
		        } elseif (isset($previous_assignment)) {
		            echo "<p><strong>Vad hände för $role->Name?</strong><br>";
		            if (isset($previous_assignment->WhatHappened) && $previous_assignment->WhatHappened != "")
		                echo nl2br(htmlspecialchars($previous_assignment->WhatHappened));
		                else echo "Inget rapporterat";
		                echo "</p>";
		                echo "<p><strong>Vad hände för andra?</strong><br>";
		                if (isset($previous_assignment->WhatHappendToOthers) && $previous_assignment->WhatHappendToOthers != "")
		                    echo nl2br(htmlspecialchars($previous_assignment->WhatHappendToOthers));
		                    else echo "Inget rapporterat";
		                    echo "</p>";
		        }
		                
		    }
		    
		    if (!empty($role->PreviousLarps)) {
		        echo "<div class='border'><h3>Tidigare</h3>";
		        echo "<p>".nl2br(htmlspecialchars($role->PreviousLarps))."</p></div>";
		    }
		    echo "</div>";
		    echo "</div>";
		}
			    
			
			
		?>


</body>
</html>
