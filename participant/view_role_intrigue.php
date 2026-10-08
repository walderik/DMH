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
$isPc = $role->isPC($current_larp);

if ($isPc) $person = $role->getPerson();

if ($isPc && $person->Id != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

$group = $role->getGroup();

$isAssignedToMe = false;
if ($role->isNPC($current_larp)) {
    $assignment = NPC_assignment::getAssignment($role, $current_larp);
    if (!empty($assignment) && ($assignment->PersonId == $current_person->Id)) {
        $isAssignedToMe = true;
    } elseif (!empty($group) && ($current_person->isMemberGroup($group) || $current_person->hasNPCInGroup($group, $current_larp))) {
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

$campaign = $current_larp->getCampaign();


include 'navigation.php';
?>
		
		<?php 
		if ($isPc || $isAssignedToMe) {
		?>
		<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Intrig för <?php echo $role->getViewLink() ?>
			<?php 
			if ($isPc) echo " <a href='character_sheet.php?id=" . $role->Id . "&bara_intrig=1' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
			?>
			
		</div>
			<div class='itemcontainer'>
			<?php if ($current_larp->isIntriguesReleased()) {
			    if (!empty($larp_role) && !empty($larp_role->Intrigue)) echo "<p>".nl2br(htmlspecialchars($larp_role->Intrigue)) ."</p>"; 
			    
			    $intrigues = $role->getAllIntriguesIncludingSubdivisionsSorted($current_larp);
			    $subdivisions = Subdivision::allForRole($role, $current_larp);
			    
		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {		    

		                $hasCommonText = false;
		                $commonTextHeader = "";
		                $intrigueTextArr = array();
		                $offTextArr = array();
		                $whatHappenedTextArr = array();
		                
		                $intrigue->findAllInfoForRoleInIntrigue($role, $subdivisions, $hasCommonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr);
		                
                       
		                echo participantPrintedIntrigue($intrigue->Number, $intrigue->CommonText, $hasCommonText, $commonTextHeader, $intrigueTextArr, $offTextArr, $whatHappenedTextArr, false);
		                
		                
		            }
		        }
			        
			    
			    
			    
			    

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
		        
		        $isMareld = $current_larp->getCampaign()->is_me();
		        
		        
		        
		        
		        if (!empty($known_groups) || !empty($known_roles) || !empty($known_props) || !empty($known_pdfs)) {
			        echo "<h3>Känner till</h3>";
			        foreach ($known_pdfs as $known_pdf) {
			            $intrigue_pdf = $known_pdf->getIntriguePDF();
			            echo "<a href='view_intrigue_pdf.php?id=$intrigue_pdf->Id' target='_blank'>$intrigue_pdf->Filename</a>";
			            echo "<br>";
			        }
			        
			        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			        foreach ($known_groups as $known_group) {
			            if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
			            else echo "<li style='display:table-cell; width:49%;'>\n";
			            echo "<div class='name'><a href='view_known_group.php?id=$known_group->Id'>$known_group->Name</a></div>";
			            if ($known_group->DescriptionForOthers !="") {
			                echo nl2br(htmlspecialchars($known_group->DescriptionForOthers));
			                if ($isMareld) echo "<br>Färg: $known_group->Colour";
			            }
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
			            if (!empty($role_group) && !$role_group->hasInvisibility()) {
			                echo "<div>$role_group->Name</div>";
			            }
			            if ($known_role->isPC($current_larp) && !$known_role->isRegistered($current_larp)) echo "<div>Spelas inte</div>";
			            elseif ($known_role->isNPC($current_larp) && !$known_role->isAssigned($current_larp)) echo "<div>Spelas inte</div>";
			            
			            echo "<div class='description'>$known_role->DescriptionForOthers</div>\n";
			            
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
		
		


</body>
</html>
