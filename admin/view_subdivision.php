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


function print_role(Role $role) {
    global $current_larp, $type;
    
    if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
    else echo "<li style='display:table-cell; width:49%;'>\n";
  
    echo "<div class='name'>".$role->getViewLink()."</div>\n";
    echo "Yrke: ".$role->Profession . "<br>";
    if ($role->isMain($current_larp)==0) {
        echo "Sidokaraktär<br>";
    }
    echo "Spelas av ".$role->getPerson()->Name."<br>";
    
    if ($role->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        $guardian = $role->getRegistration($current_larp)->getGuardian();
        if (isset($guardian)) echo "Ansvarig vuxen är " . $guardian->Name;
        else echo "Ansvarig vuxen är inte utpekad.";
    }
    
    echo "<div class='description'>$role->DescriptionForOthers</div>\n";
    if ($role->hasImage()) {
        $image = Image::loadById($role->ImageId);
        echo "<img src='../includes/display_image.php?id=$role->ImageId'/>\n";
        if (!empty($image->Photographer)) {
            echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
        }
    }
    else {
        echo "<img src='../images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    }
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>


	<div class="content">
		<h1><?php echo $subdivision->Name;?> 
		</h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br(htmlspecialchars($subdivision->Description));?></td></tr>
		</table>		
		
		

		<?php 
		function compare_objects($obj_a, $obj_b) {
		    return $obj_a->Id - $obj_b->Id;
		}
		
		
		$registered_characters_in_subdivision = $subdivision->getAllRegisteredMembers($current_larp);
		$not_registered_characters = array_udiff($subdivision->getAllMembers(), $registered_characters_in_subdivision, 'compare_objects');
		
		if (!empty($registered_characters_in_subdivision)) {
		    echo "<h2>Medlemmar som kommer på lajvet</h2>";
		    
		    echo "<div class='container'>\n";
		    
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($registered_characters_in_subdivision as $role) {
		        print_role($role);
		        $temp++;
		        if($temp==$columns)
		        {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		        
		    }
		    $temp=0;
		    
		    echo "</ul>\n";
	       echo "</div>\n";
		}
		
		if (!empty($not_registered_characters)) {
		    echo "<h2>Medlemmar som inte är anmälda</h2>";
		    
		    echo "<div class='container'>\n";
		    
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($not_registered_characters as $role) {
		        print_role($role);
		        $temp++;
		        if($temp==$columns)
		        {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		        
		    }
		    $temp=0;
		    
		    echo "</ul>\n";
		    echo "</div>\n";
		}
		
		
		?>

		</div>    

		<h2>Intrig</h2>
		<div>

			<?php 
			if ($current_larp->isIntriguesReleased()) {
			    
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
			}

			else {
			    echo "Intrigerna är inte klara än.";
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
