<?php

require 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $subdivisionId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
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

$subdivision = Subdivision::loadById($subdivisionId); 

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
} else {
    $referer = "view_subdivision.php?id=$subdivision->Id";
}

function print_role($role, $subdivision, $isAtLarp, $mayRemove) {
    global $current_larp, $referer;

    
    echo "<tr>";
    echo "<td>";
    if ($role->hasImage()) {
        echo "<img width='30' src='../includes/display_image.php?id=$role->ImageId'/>\n";
    }
    echo "</td>";
    
    echo "<td>";
    echo $role->getViewLink();
    if ($isAtLarp) { 
        $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
        if ($larp_role->IsMainRole != 1) {
            echo " (Sidokaraktär)";
        }
    }
    
    echo "</td>";
    echo "<td>";
    echo $role->getEditLinkPen(true);
    if ($mayRemove) {
        echo "<form id='delete_member_$role->Id' action='subdivision_form.php' method='post' class='fabutton'>";
        echo " ";
        echo "<input form='delete_member_$role->Id' type='hidden' id='operation' name='operation' value='delete_member'>";
        echo "<input form='delete_member_$role->Id' type='hidden' id='id' name='id' value='$subdivision->Id'>";
        echo "<input form='delete_member_$role->Id' type='hidden' id='Referer' name='Referer' value='$referer'>";
        echo "<input form='delete_member_$role->Id' type='hidden' id='ReturnTo' name='ReturnTo' value='view_subdivision.php?id=$subdivision->Id'>";
        echo "<input form='delete_member_$role->Id' type='hidden' id='memberId' name='memberId' value='$role->Id'>";
        echo "<button form='delete_member_$role->Id' class='invisible' type='submit'><i class='fa-solid fa-trash' title='Ta bort från grupperingen'></i></button>";
        echo "</form>";
    }
    
    echo "</td>";
    echo "<td>$role->Profession</td>";
    
    $person = $role->getPerson();
    echo "<td><a href ='view_person.php?id=$person->Id'>$person->Name</a></td>";
    
    
    echo "<td>";
    if ($isAtLarp && $role->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        echo "Ansvarig vuxen är ";
        $registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
        if (!empty($registration->GuardianId)) {
            $guardian = $registration->getGuardian();
            echo "<a href ='view_person.php?id=$guardian->Id'>$guardian->Name</a>";
        } else echo showStatusIcon(false);
        
    }
    
    echo "</td>";
    
    echo "</tr>";
}

$ruletextarray = $subdivision->getRuleTextArray();
include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $subdivision->Name." ".$subdivision->getEditLinkPen();?> 
		</h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br(htmlspecialchars($subdivision->Description));?></td></tr>
			<tr><td valign="top" class="header">Synlig för medlemmar</td><td><?php echo ja_nej($subdivision->isVisibleToParticipants());?></td></tr>
			<tr><td valign="top" class="header">Kan se vilka som är medlemmar</td><td><?php echo ja_nej($subdivision->canSeeOtherParticipants());?></td></tr>
			<tr><td valign="top" class="header">Automatisk tilldelning</td><td><?php echo implode("<br>",$ruletextarray);?></td></tr>
		</table>		
		
		

		<?php 
		
		function compare_objects($obj_a, $obj_b) {
		    return $obj_a->Id - $obj_b->Id;
		}
		
		
		$registered_automatic_characters_in_subdivision = $subdivision->getAllAutomaticRegisteredMembers($current_larp);
		$registered_manual_characters_in_subdivision = $subdivision->getAllManualRegisteredMembers($current_larp);
		$not_registered_characters = $subdivision->getAllManualMembersNotComing($current_larp);
		

	    $personIdArr = array();
	    foreach ($registered_automatic_characters_in_subdivision as $role) {
	        $person = $role->getPerson();
	        if ($person->isNotComing($current_larp)) continue;
	        $personIdArr[] = $person->Id;
	    }
	    foreach ($registered_manual_characters_in_subdivision as $role) {
	        $person = $role->getPerson();
	        if ($person->isNotComing($current_larp)) continue;
	        $personIdArr[] = $person->Id;
	    }
	    echo contactSeveralEmailIcon("Maila alla som är med i $subdivision->Name", $personIdArr,
		        "Medlem i $subdivision->Name på $current_larp->Name",
		        "Meddelande till alla som är med i $subdivision->Name på $current_larp->Name");
		    
	    

		echo "<h2>Automatiskt tillagda medlemmar som kommer på $current_larp->Name</h2>";
		echo "<strong>Regel: ". implode(" och ", $ruletextarray)."</strong><br>";
		echo "<table>";
		foreach ($registered_automatic_characters_in_subdivision as $role) {
		    print_role($role, $subdivision, true, false);
		}
		
		echo "</table>\n";
		


		echo "<h2>Manuellt tillagda medlemmar som kommer på $current_larp->Name</h2>";
		    
		    
        echo "<br><br>";
        echo "<form id='add_member' action='choose_role.php' method='post'></form>";
        echo "<input form='add_member' type='hidden' id='id' name='id' value='$subdivision->Id'>";
        echo "<input form='add_member' type='hidden' id='2ndReferer' name='2ndReferer' value='$referer'>";
        echo "<input form='add_member' type='hidden' id='ReturnTo' name='ReturnTo' value='view_subdivision.php?id=$subdivision->Id'>";
        echo "<input form='add_member' type='hidden' id='operation' name='operation' value='add_subdivision_member'>";
        echo "<button form='add_member' class='invisible' type='submit'><i class='fa-solid fa-plus' title='Lägg till karaktär(er) som är med i grupperingen'></i><i class='fa-solid fa-user' title='Lägg till karaktär(er) som är med i grupperingen'></i></button>";
        echo "<table>";
        foreach ($registered_manual_characters_in_subdivision as $role) {
	        print_role($role, $subdivision, true, true);
	    }
	    
	    echo "</table>\n";

		
		if (!empty($not_registered_characters)) {
		    echo "<h2>Manuellt tillagda medlemmar som inte är anmälda</h2>";
		    
		    echo "<table>\n";
		    foreach ($not_registered_characters as $role) {
		        print_role($role, $subdivision, false, true);
		    }
		    echo "</table>\n";
		}
		
		
		?>

		</div>    

		<h2>Intrig</h2>
		<div>

			<?php 
			    
		    $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
		    if (!empty($intrigues)) {
		        echo "<table class='data'>";
		        echo "<tr><th>Intrig</th><th>Intrigtext</th><th></th></tr>";
		        foreach ($intrigues as $intrigue) {
		            echo "<tr>";
		            echo "<td><a href='view_intrigue.php?Id=$intrigue->Id'>Intrigspår: $intrigue->Number. $intrigue->Name</a>";
		            if (!$intrigue->isActive()) echo " (inte aktuell)";
		            echo "</td>";
		            $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
		            echo "<td>";
		            if ($intrigue->isActive()) {
		                if (!empty($intrigue->CommonText)) echo nl2br(htmlspecialchars($intrigue->CommonText))."<br><br>";
		                echo nl2br($intrigueActor->IntrigueText);
		                if (!empty($intrigueActor->OffInfo)) {
		                    echo "<br><br><strong>Off-information:</strong><br>".nl2br($intrigueActor->OffInfo);
		                }
		                if (!empty($intrigueActor->WhatHappened)) {
		                    echo "<br><br><strong>Vad hände:</strong><br>".nl2br(htmlspecialchars($intrigueActor->WhatHappened));
		                }
		                
		                echo "</td>";
		                echo "<td><a href='actor_intrigue_form.php?IntrigueActorId=$intrigueActor->Id&name=$subdivision->Name'><i class='fa-solid fa-pen'></i></a></td>";
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
		        echo "<br>";
		    }
            $known_groups = $subdivision->getAllKnownGroups($current_larp);
            $known_roles = $subdivision->getAllKnownRoles($current_larp);
            $known_npcgroups = $subdivision->getAllKnownNPCGroups($current_larp);
            $known_npcs = $subdivision->getAllKnownNPCs($current_larp);
            $known_props = $subdivision->getAllKnownProps($current_larp);
            $known_pdfs = $subdivision->getAllKnownPdfs($current_larp);
            
            $checkin_letters = $subdivision->getAllCheckinLetters($current_larp);
            $checkin_telegrams = $subdivision->getAllCheckinTelegrams($current_larp);
            $checkin_props = $subdivision->getAllCheckinProps($current_larp);
            
            if (!empty($known_groups) || !empty($known_roles) || !empty($known_npcs) || !empty($known_props) || !empty($known_npcgroups)) {
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
		                $image = Image::loadById($prop->ImageId);
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
	            $cols=5;
	            foreach ($checkin_props as $checkin_prop) {
	                $prop=$checkin_prop->getIntrigueProp()->getProp();
	                echo "<li style='display:table-cell; width:19%;'>\n";
	                echo "<div class='name'>$prop->Name</div>\n";
	                if ($prop->hasImage()) {
	                    $image = Image::loadById($prop->ImageId);
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
			
			
					<?php 
		$previous_larps = LARP::getPreviousLarpsInCampaign($current_larp);
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    
		    echo "<h2>Historik</h2>";
		    foreach ($previous_larps as $prevoius_larp) {
		        $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $prevoius_larp->Id);
		        if (empty($intrigues)) continue;
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        
		        foreach($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<div class='intrigue'>";
		                echo "<p><strong>Intrig</strong><br>".nl2br($intrigueActor->IntrigueText)."</p>";
		                
		                echo "<p><strong>Vad hände med det?</strong><br>";
		                if (!empty($intrigueActor->WhatHappened)) echo nl2br($intrigueActor->WhatHappened);
		                else echo "Inget rapporterat";
		                echo "</p>";
		                echo "</div>";
		            }
		        }
		        
	            echo "</div>";
		                
		    }
		}
			    
			
			
		?>
			
			
			
		</div>

	</div>


</body>
</html>
