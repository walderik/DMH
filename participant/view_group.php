<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['GroupId'])) {
        $GroupId = $_GET['GroupId'];
        echo $GroupId;
    }
    else {
        header('Location: index.php');
    }
}

$current_group = Group::loadById($GroupId); 

if (Person::loadById($current_group->PersonId)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din grupp
}

if (!$current_group->isRegistered($current_larp)) {
    header('Location: index.php'); //Gruppen är inte anmäld
}

$larp_group = LARP_Group::loadByIds($current_group->Id, $current_larp->Id);

$group_members = Role::getRegisteredRolesInGroup($current_group, $current_larp);

function ja_nej($val) {
    if ($val == 0) return "Nej";
    if ($val == 1) return "Ja";
}

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


	<div class="content">
		<h1><?php echo $current_group->Name;?></h1>
		<table>
			<tr><td valign="top">Gruppledare</td><td><?php echo Person::loadById($current_group->PersonId)->Name;?></td></tr>
			<tr><td valign="top">Beskrivning</td><td><?php echo $current_group->Description;?></td></tr>
			<tr><td valign="top">Vänner</td><td><?php echo $current_group->Friends;?></td></tr>
			<tr><td valign="top">Fiender</td><td><?php echo $current_group->Enemies;?></td></tr>
			<tr><td valign="top">Rikedom</td><td><?php echo Wealth::loadById($current_group->WealthId)->Name;?></td></tr>
			<tr><td valign="top">Var bor gruppen?</td><td><?php echo PlaceOfResidence::loadById($current_group->PlaceOfResidenceId)->Name;?></td></tr>
			<tr><td valign="top">Intrigidéer</td><td><?php echo $current_group->IntrigueIdeas;?></td></tr>
			<tr><td valign="top">Annan information</td><td><?php echo $current_group->OtherInformation;?></td></tr>
			<tr><td valign="top">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue);?></td></tr>
			<tr><td valign="top">Antal medlemmar</td><td><?php echo $current_group->ApproximateNumberOfMembers;?></td></tr>
			<tr><td valign="top">Eldplats</td><td><?php echo ja_nej($current_group->NeedFireplace);?></td></tr>
			<tr><td valign="top">Boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
		
		
		
		<?php 
		
		
		//TODO valda intrigtyper
		
		//TODO Anmälda medlemmar
		?>
		<h2>Medlemmar</h2>
		<?php 
		foreach($group_members as $group_member) {
		    echo $group_member->Name . " - " . $group_member->Profession . " spelas av " . $group_member.getPerson()->Name . "<br>"; 
		}
		
		?>
		
		<h2>Intrig</h2>
			<?php if ($current_larp->DisplayIntrigues == 1) {
			    echo $larp_group->Intrigue;    
			}
			else {
			    echo "Intrigerna är inte klara än.";
			}
			?>
		
		</table>
	</div>


</body>
</html>
