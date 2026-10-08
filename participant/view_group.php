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


$group = Group::loadById($GroupId); 

if (!$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group) && !$current_person->hasNPCInGroup($group, $current_larp)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

$larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);


$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);
$non_main_characters_in_group = Role::getAllNonMainRolesInGroup($group, $current_larp);
$allUnregisteredRoles = Role::getAllUnregisteredRolesInGroup($group, $current_larp);
$NPCs_in_group = Role::getAllNPCsInGroup($group);

function print_role(Role $role, Group $group, $isComing) {
    global $current_person, $current_larp, $type;
    
    if($type=="Computer") echo "<li style='display:table-cell; width:19%;'>\n";
    else echo "<li style='display:table-cell; width:49%;'>\n";
    
    
    echo "<div class='name'>";
    if ($role->isNPC($current_larp)) {
        echo $role->getViewLink();
    } else echo $role->Name;

    $isPc = $role->isPC($current_larp);
    if (($isPc && $current_person->isGroupLeader($group)) || 
        ($role->isNPC($current_larp) && ($role->CreatorPersonId == $current_person->Id || $current_person->isGroupLeader($group)) && $role->userMayEdit() && $role->mayDelete())) {
        echo " <a href='logic/remove_group_member.php?groupID=".$group->Id."&roleID=".$role->Id."' onclick=\"return confirm('Är du säker på att du vill ta bort karaktären från gruppen?');\">";
        echo "<i class='fa-solid fa-trash-can'></i>";
        echo "</a>";
    }
    echo "</div>\n";

    
    echo "<b>Yrke:</b> ".$role->Profession . "<br>";
    if ($isComing && $isPc && $role->isMain($current_larp)==0) {
        echo "<b>Sidokaraktär</b><br>";
    }

    

    if ($isPc) {
        $person =  $role->getPerson();
        if ($person->hasPermissionShowName()) {
            echo "<b>Spelas av:</b> $person->Name<bar>";
        }
    }

    
    if ($isComing && $isPc && $person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        $guardian = $role->getRegistration($current_larp)->getGuardian();
        if (isset($guardian)) echo "Ansvarig vuxen är " . $guardian->Name;
        else echo "Ansvarig vuxen är inte utpekad.";
    }
    
    echo "<div class='description'>$role->DescriptionForGroup</div>\n";
    
    if (!$isPc) {
        if ($role->isApproved()) echo "Karaktären är godkänd.";
        elseif (!$role->userMayEdit()) echo "Karaktären är inskickad för godkännande.";
        else {
            echo "<form action='logic/role_lock.php' method='post'>";
            echo "<input type='hidden' id='Id' name='Id' value='$role->Id'>";
            echo "<button type='$type' name='SaveAndLockButton' value='1' class='button-18'><i class='fa-solid fa-share'></i> ";
            echo "Skicka in för godkännande</button>";
            echo "</form>";
        }
        
        echo "<br>";
    }
    
    
    if ($role->hasImage()) {
        $image = Image::loadById($role->ImageId);
        echo "<img src='../includes/display_image.php?id=$role->ImageId'/>\n";
        if (!empty($image->Photographer)) {
            echo "<div class='photographer'>Fotograf $image->Photographer</div>\n";
        }
    }
    elseif ($isPc) {
        echo "<img src='../images/man-shape.png' />\n";
        echo "<div class='photographer'><a href='https://www.flaticon.com/free-icons/man' title='man icons'>Man icons created by Freepik - Flaticon</a></div>\n";
    } 
    echo "</li>\n\n";
    
}

include 'navigation.php';
?>

	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-people-group"></i>
			<?php echo $group->Name;?>
			<a href='group_sheet.php?id=<?php echo $group->Id;?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Gruppblad'></i></a>
			<?php 
			if ($current_person->isGroupLeader($group) && (!$group->isRegistered($current_larp) || $group->userMayEdit($current_larp))) {
			    echo " " . $group->getEditLinkPen(false);
			}
			?>
    		
		</div>
		
	   <div class='itemcontainer'>
		<?php 
		if ($current_larp->isIntriguesReleased()) {
			echo "<a href='view_group_intrigue.php?id=$group->Id'>Intriger</a><br>"; 
		}
		echo "<a href='view_group_history.php?id=$group->Id'>Historik</a>";
		?>
	   </div>
		
		
		<?php 
		if ($group->hasImage()) {
		    echo "<div class='itemcontainer'>";
		    $image = Image::loadById($group->ImageId);
		    echo "<img width='300' src='../includes/display_image.php?id=$group->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</div>";
		}
		?>

	   <div class='itemcontainer'>
       <div class='itemname'>Gruppansvarig</div>
	   <?php 
	   $person = $group->getPerson();
	   if (!empty($person)) echo $person->Name;
	   ?>
	   </div>

	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning</div>
	   <?php echo nl2br(htmlspecialchars($group->Description));?>
	   </div>

	   <div class='itemcontainer'>
       <div class='itemname'>Beskrivning för andra</div>
	   <?php echo nl2br(htmlspecialchars($group->DescriptionForOthers));?>
	   </div>

	   <div class='itemcontainer'>
       <div class='itemname'>Vänner</div>
	   <?php echo nl2br(htmlspecialchars($group->Friends));?>
	   </div>
			
	   <div class='itemcontainer'>
       <div class='itemname'>Fiender</div>
	   <?php echo nl2br(htmlspecialchars($group->Enemies));?>
	   </div>

		<?php if (Wealth::isInUse($current_larp)) {?>
		   <div class='itemcontainer'>
           <div class='itemname'>Rikedom</div>
    	   <?php 
    	   $wealth = $group->getWealth();
    	   if (!empty($wealth)) echo $wealth->Name; 
    	   ?>
    	   </div>
		<?php }?>
		
		<?php if (PlaceOfResidence::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Var bor gruppen?</div>
    	   <?php 
    	   $por = $group->getPlaceOfResidence();
    	   if (!empty($por)) echo $group->getPlaceOfResidence()->Name; ?>
    	   </div>
		<?php }?>


		<?php if (GroupType::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Typ av grupp</div>
    	   <?php 
    	   $gt = $group->getGroupType();
    	   if (!empty($gt)) echo $gt->Name; ?>
    	   </div>
		<?php }?>

		<?php if (ShipType::isInUse($current_larp)) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Typ av skepp</div>
    	   <?php 
    	   $st = $group->getShipType();
    	   if (!empty($st)) echo $st->Name; ?>
    	   </div>
		<?php }?>
		
		<?php if ($current_larp->getCampaign()->is_me()) { ?>
		   <div class='itemcontainer'>
           <div class='itemname'>Färg</div>
    	   <?php echo $group->Colour; ?>
    	   </div>
		<?php }?>
	   <div class='itemcontainer'>
       <div class='itemname'>Annan information</div>
	   <?php echo nl2br(htmlspecialchars($group->OtherInformation)); ?>
	   </div>
	   <?php  if (isset($larp_group)) { ?>
	   
    	   <div class='itemcontainer'>
           <div class='itemname'>Önskar intrig</div>
    	   <?php echo ja_nej($larp_group->WantIntrigue); ?>
    	   </div>
    
    		<?php if ($current_person->isGroupLeader($group)) { ?>
    		   <div class='itemcontainer'>
               <div class='itemname'>Intrigidéer</div>
        	   <?php echo nl2br(htmlspecialchars($larp_group->IntrigueIdeas));?>
        	   </div>
    		<?php } ?>

    	   <div class='itemcontainer'>
           <div class='itemname'>Kvarvarande intriger</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->RemainingIntrigues)); ?>
    	   </div>
    
   	   	   <div class='itemcontainer'>
           <div class='itemname'>Vad har hänt?</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->WhatHappenedSinceLastLarp)); ?>
    	   </div>
    
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Uppskattat antal medlemmar på lajvet</div>
    	   <?php echo $larp_group->ApproximateNumberOfMembers;?>
    	   </div>
    
	   
    	   <div class='itemcontainer'>
           <div class='itemname'>Önskat boende</div>
    	   <?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Typ av tält</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentType)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Storlek på tält</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentSize)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Vilka ska bo i tältet</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentHousing)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Önskad placering</div>
    	   <?php echo nl2br(htmlspecialchars($larp_group->TentPlace)); ?>
    	   </div>
    
    	   <div class='itemcontainer'>
           <div class='itemname'>Eldplats</div>
    	   <?php echo ja_nej($larp_group->NeedFireplace);?>
    	   </div>
	   <?php  }?>

		<div class='itemcontainer'>
		<div class='itemname'>Anmälda medlemmar</div>
		
		<?php 
		echo "<div class='container' style ='box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if (empty($main_characters_in_group) && empty($non_main_characters_in_group)) {
		    echo "Inga anmälda i gruppen än.";
		}
		else {
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($main_characters_in_group as $role) {
		        print_role($role, $group, true);
		        $temp++;
		        if($temp==$columns) {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		    }
		    $temp=0;
		    echo "</ul>\n";

		    if (!empty($non_main_characters_in_group)) {
		        echo "<div class='itemname'>Sidokarktärer</div>";
    		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
    		    foreach ($non_main_characters_in_group as $role) {
    		        print_role($role, $group, true);
    		        $temp++;
    		        if($temp==$columns) {
    		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
    		            $temp=0;
    		        }
    		    }
    		    $temp=0;
    		    echo "</ul>\n";
		    }
		}
		
		echo "</div>\n";
		?>
		</div>
		
		<?php
		if(!empty($allUnregisteredRoles)) {
		    echo "<div class='itemcontainer'>";
		    echo "<div class='itemname'>Icke anmälda medlemmar</div>";

			$temp=0;
			echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
			foreach($allUnregisteredRoles as $role) {
			    print_role($role, $group, false);
			    $temp++;
			    if($temp==$columns) {
			        echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
			        $temp=0;
			    }
			}
			$temp=0;
			echo "</ul>\n";
			echo "</div>\n";
		}
		?>
		
		<div class='itemcontainer'>
		<div class='itemname'>NPC'er i gruppen</div>

		<?php 
		echo "<div class='container' style ='box-shadow: none; margin: 0px; padding: 0px;'>\n";
		if (empty($NPCs_in_group)) {
		    echo "Det finns inga NPC'er i gruppen än.";
		}
		else {
		    echo "<ul class='image-gallery' style='display:table; border-spacing:5px;'>\n";
		    foreach ($NPCs_in_group as $role) {
		        print_role($role, $group, true);
		        $temp++;
		        if($temp==$columns) {
		            echo"</ul>\n<ul class='image-gallery' style='display:table; border-spacing:5px;'>";
		            $temp=0;
		        }
		    }
		    $temp=0;
		    echo "</ul>\n";

		}
		
		echo "</div>\n";
		?>
		<div class='center'><a href='role_form.php?action=insert&type=npc&groupId=<?php echo $group->Id ?>'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-person'></i> &nbsp;Skapa ny NPC i gruppen</button></a></div>


		
		</div>
		
		
		
		</div>


</body>
</html>
