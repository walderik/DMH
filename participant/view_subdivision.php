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

if (!$current_person->isMemberSubdivision($subdivision)) {
    header('Location: index.php'); //Inte medlem i grupperingen
    exit;
}

if (!$subdivision->isVisibleToParticipants()) {
    header('Location: index.php'); //Grupperingen är inte synlig
    exit;
}

$registered_characters_in_subdivision = null;


function compare_objects($obj_a, $obj_b) {
    return $obj_a->Id - $obj_b->Id;
}


if ($subdivision->canSeeOtherParticipants()) {
    $registered_characters_in_subdivision = $subdivision->getAllRegisteredMembers($current_larp);
    $not_registered_characters = $subdivision->getAllManualMembersNotComing($current_larp);
}

function print_role(Role $role, bool $isComing) {
    global $current_larp, $type;
    
    if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
    else echo "<li style='display:table-cell; width:49%;'>\n";
  
    echo "<div class='name'>$role->Name";
    echo "</div>\n";
    echo "Yrke: ".$role->Profession . "<br>";
    if ($isComing) {
        if ($role->isMain($current_larp)==0) {
            echo "Sidokaraktär<br>";
        }
        $person = $role->getPerson();
        if (is_null($person)) echo "NPC";
        elseif ($person->hasPermissionShowName()) {
            echo "<div>Spelas av $person->Name</div>";
        }
        
        
        if (!is_null($person) && $person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
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
    }
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-people-group"></i>
			<?php echo $subdivision->Name;?>
		</div>
	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($subdivision->Description));?>
	   </div>


		<?php 
		
		if (!empty($registered_characters_in_subdivision)) {
		    echo "<div class='itemcontainer'>";
		    echo "<div class='itemname'>Medlemmar som kommer på lajvet</div>";
		    
		    echo "<div class='container'>\n";
		    
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($registered_characters_in_subdivision as $role) {
		        print_role($role, true);
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
	       echo "</div>";
		}
		
		if (!empty($not_registered_characters)) {
		    echo "<div class='itemcontainer'>";
		    echo "<div class='itemname'>Medlemmar som inte är anmälda</div>";
		    
		    echo "<div class='container'>\n";
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($not_registered_characters as $role) {
		        print_role($role, false);
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

		<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Intrig
		</div>
			<div class='itemcontainer'>

			<?php 
			if ($current_larp->isIntriguesReleased()) {
			    
			    $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
			    $intrigue_numbers = array();
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
		                if (!empty($intrigue->CommonText)) echo "<p>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		                if (!empty($intrigueActor->IntrigueText)) echo "<p>".nl2br(htmlspecialchars($intrigueActor->IntrigueText)). "</p>";
		                if (!empty($intrigueActor->OffInfo)) {
		                    echo "<p><strong>Off-information:</strong><br><i>".nl2br(htmlspecialchars($intrigueActor->OffInfo))."</i></p>";
		                }
		                
		                if (!empty($intrigueActor->IntrigueText) || !empty($intrigue->CommonText) || !empty($intrigueActor->OffInfo)) {
		                    $intrigue_numbers[] = $intrigue->Number;
		                    echo "<hr>";
		                }
		            }
		        }
		        if (!empty($intrigue_numbers)) {
		            echo "<p>Intrignummer " . implode(', ', $intrigue_numbers).". De kan behövas om du behöver hjälp av arrangörerna med en intrig under lajvet.</p>";
                }
                
                $known_groups = $subdivision->getAllKnownGroups($current_larp);
                $known_roles = $subdivision->getAllKnownRoles($current_larp);
                $known_props = $subdivision->getAllKnownProps($current_larp);
                $known_pdfs = $subdivision->getAllKnownPdfs($current_larp);
                
                $checkin_letters = $subdivision->getAllCheckinLetters($current_larp);
                $checkin_telegrams = $subdivision->getAllCheckinTelegrams($current_larp);
                $checkin_props = $subdivision->getAllCheckinProps($current_larp);
                
                if (!empty($known_groups) || !empty($known_roles) || !empty($known_props)) {
			        echo "<h3>Känner till</h3>";
			        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			        foreach ($known_groups as $known_group) {
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'>$known_group->Name</div>";
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
			            echo "<div class='name'>$known_role->Name</div>";
			            $role_group = $known_role->getGroup();
			            if (!empty($role_group)) {
			                echo "<div>$role_group->Name</div>";
			            }
			            if ($known_role->isPC($current_larp) && !$known_role->isRegistered($current_larp)) echo "<div>Spelas inte</div>";
			            elseif ($known_role->isNPC($current_larp) && !$known_role->isAssigned($current_larp)) echo "<div>Spelas inte</div>";
			            
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
			        foreach ($known_props as $known_prop) {
			            $prop = $known_prop->getIntrigueProp()->getProp();
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'>$prop->Name</div>\n";
			            if ($prop->hasImage()) {
			                $image = Image::loadById($prop->ImageId);
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
		                echo "<li style='display:table-cell; width:19%;'>\n";
		                echo "<div class='name'>$prop->Name</div>\n";
		                if ($prop->hasImage()) {
		                    $image = Image::loadById($prop->ImageId);
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
			}

			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
			</div>
			</div>
			
			
					<?php 
		$previous_larps = LARP::getPreviousLarpsInCampaign($current_larp);
		if (isset($previous_larps) && count($previous_larps) > 0) {
		    echo "<div class='itemselector'>";
		    echo "<div class='header'>";
		    
		    echo "<i class='fa-solid fa-landmark'></i> Historik";
		    echo "</div>";
		    echo "<div class='itemcontainer'>";
		    
		    foreach ($previous_larps as $prevoius_larp) {
		        $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $prevoius_larp->Id);
		        if (empty($intrigues)) continue;
		        echo "<div class='border'>";
		        echo "<h3>$prevoius_larp->Name</h3>";
		        
		        foreach($intrigues as $intrigue) {
		            $intrigueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
		            if ($intrigue->isActive() && !empty($intrigueActor->IntrigueText)) {
		                echo "<p><strong>Intrig</strong><br>".nl2br(htmlspecialchars($intrigueActor->IntrigueText))."</p>";
		                
		                echo "<p><strong>Vad hände med det?</strong><br>";
		                if (!empty($intrigueActor->WhatHappened)) echo nl2br(htmlspecialchars($intrigueActor->WhatHappened));
		                else echo "Inget rapporterat";
		                echo "</p>";
		            }
		        }
		        
	            echo "</div>";
		                
		    }
		    
		    echo "</div>";
		    echo "</div>";
		    
		}
			    
			
			
		?>
			
			


</body>
</html>
