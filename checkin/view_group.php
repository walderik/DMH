<?php

include_once 'header.php';

$action = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
    if (isset($_GET['action']))  {
        $action = $_GET['action'];
    }
}

if (empty($action)) {
    if ($current_larp->isCheckoutTime()) $action = "checkout";
    else $action = "checkin";
}





$group = Group::loadById($GroupId); 

if (!$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group) && !$current_person->hasNPCInGroup($group, $current_larp)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

$larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);


$main_characters_in_group = Role::getAllMainRolesInGroup($group, $current_larp);

function print_person_info(Person $person, $action) {
    global $current_larp;
    $registration = $person->getRegistration($current_larp);
    if ($action == "checkin") {
        echo "<a href='checkin_person.php?id=$person->Id'>";
    } elseif ($action == "checkout") {
        echo "<a href='checkout_person.php?id=$person->Id'>";
    }
    echo $person->Name."</a> ";
    if ($action == "checkin") {
        echo showStatusIcon($registration->isCheckedIn(), "checkin_person.php?id=".$person->Id, null, "Inte incheckad", "Redan incheckad");
    } elseif ($action == "checkout") {
        echo showStatusIcon($registration->isCheckedIn(), "checkout_person.php?id=".$person->Id, null, "Inte utcheckad", "Redan utcheckad");
    }
    
}

function print_role(Role $role, $action) {
    global $current_larp;
    echo "<tr>";
    
    echo "<td>";
    echo $role->Name;
    echo "</td>";
    

    $person = $role->getPerson();
    echo "<td>";
    print_person_info($person, $action);
    echo "</td>";
    
    echo "<td>";
    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
        echo "Ansvarig vuxen är ";
        $registration = Registration::loadByIds($role->PersonId, $current_larp->Id);
        if (!empty($registration->GuardianId)) {
            $guardian = $registration->getGuardian();
            print_person_info($guardian, $action);
        } else echo showStatusIcon(false, null, null, "Saknar ansvarig vuxen");
    }
    
    
    echo "</td>";

    
    echo "</tr>";
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
       <div class='itemname'>Gruppansvarig</div>
	   <?php 
	   $person = $group->getPerson();
	   if (!empty($person)) print_person_info($person, $action);
	   ?>
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
		


		<div class='itemcontainer'>
		<div class='itemname'>Medlemmar</div>
		<table class ='smalldata'>
		<?php 
		    foreach ($main_characters_in_group as $role) {
		        print_role($role, $action);
		    }
		

		?>
		</table>
		</div>
		
		

		    
		    
		    

</body>
</html>
