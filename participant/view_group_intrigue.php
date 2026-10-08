<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}



$group = Group::loadById($GroupId); 

if (!$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group) && !$current_person->hasNPCInGroup($group, $current_larp)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

$larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);

include 'navigation.php';
?>

		    
		<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-scroll"></i> Intrig för <?php echo $group->getViewLink() ?>
			<a href='group_sheet.php?id=<?php echo $group->Id;?>&bara_intrig=1' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad'></i></a>
			
		</div>
		<div class='itemcontainer'>
			<?php 
			if ($current_larp->isIntriguesReleased()) {
			    if (!empty($larp_group) && !empty($larp_group->Intrigue)) echo "<p>".nl2br($larp_group->Intrigue) ."</p>"; 
			    
			    
			    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);

		        foreach ($intrigues as $intrigue) {
		            if ($intrigue->isActive()) {
		                $intrigueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
		                $txt = "";
		                if (!empty($intrigue->CommonText) && $intrigueActor->hasCommonText()) $txt .= "<p>".nl2br(htmlspecialchars($intrigue->CommonText))."</p>";
		                if (!empty($intrigueActor->IntrigueText)) $txt .=  "<p>".nl2br($intrigueActor->IntrigueText). "</p>";
		                if (!empty($intrigueActor->OffInfo)) {
		                    $txt .=  "<p><strong>Off-information:</strong><br><i>".nl2br($intrigueActor->OffInfo)."</i></p>";
		                }
		                
		                if (!empty($txt)) {
		                    echo "Intrig $intrigue->Number:<br>".$txt."<hr>";

		                }
		            }
		        }
                
                $known_groups = $group->getAllKnownGroups($current_larp);
                $known_roles = $group->getAllKnownRoles($current_larp);
                $known_props = $group->getAllKnownProps($current_larp);
                $known_pdfs = $group->getAllKnownPdfs($current_larp);
                
                $checkin_letters = $group->getAllCheckinLetters($current_larp);
                $checkin_telegrams = $group->getAllCheckinTelegrams($current_larp);
                $checkin_props = $group->getAllCheckinProps($current_larp);
                
                $isMareld = $current_larp->getCampaign()->is_me();
                
                if (!empty($known_groups) || !empty($known_roles) || !empty($known_props)) {
			        echo "<h3>Känner till</h3>";
			        echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			        $cols=5;
			        foreach ($known_groups as $known_group) {
			            echo "<li style='display:table-cell; width:19%;'>";
			            echo "<div class='name'>$known_group->Name</div>";
			            echo "<div>Grupp</div>";
			            if ($known_group->DescriptionForOthers !="") {
			                echo nl2br(htmlspecialchars($known_group->DescriptionForOthers));
			                if ($isMareld) echo "<br>Färg: $known_group->Colour";
			            }
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
		        $rumours = Rumour::allKnownByGroup($current_larp, $group);
		        if (!empty($rumours)) {
    		        echo "<h2>Rykten</h2>";
    		        foreach($rumours as $rumour) {
    		            echo $rumour->Text;
    		            echo "<br>";
    		        }
		        }
		        
			}

			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
			</div>
			</div>
			
			
			
			
			
		</div>

	</div>


</body>
</html>
