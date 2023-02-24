<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
    }
    else {
        header('Location: index.php');
    }
}

$current_group = Group::loadById($GroupId); 

if (!$current_user->isMember($group)) {
    header('Location: index.php'); //Inte medlem i gruppen
}

if (!$current_group->isRegistered($current_larp)) {
    header('Location: index.php'); //Gruppen är inte anmäld
}

$larp_group = LARP_Group::loadByIds($current_group->Id, $current_larp->Id);

$group_members = Role::getRegisteredRolesInGroup($current_group, $current_larp);

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
			<tr><td valign="top" class="header">Gruppansvarig</td><td><?php echo Person::loadById($current_group->PersonId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo $current_group->Description;?></td></tr>
			<tr><td valign="top" class="header">Vänner</td><td><?php echo $current_group->Friends;?></td></tr>
			<tr><td valign="top" class="header">Fiender</td><td><?php echo $current_group->Enemies;?></td></tr>
			<tr><td valign="top" class="header">Rikedom</td><td><?php echo Wealth::loadById($current_group->WealthId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Var bor gruppen?</td><td><?php echo PlaceOfResidence::loadById($current_group->PlaceOfResidenceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Intrigtyper</td><td><?php echo commaStringFromArrayObject($larp_group->getIntrigueTypes());?></td></tr>
			<?php if ($current_user->isGroupLeader($current_group)) { ?>
			<tr><td valign="top" class="header">Intrigidéer</td><td><?php echo $current_group->IntrigueIdeas;?></td></tr>
			<?php } ?>
			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_group->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Intrig</td><td><?php echo ja_nej($larp_group->WantIntrigue);?></td></tr>
			<tr><td valign="top" class="header">Antal medlemmar</td><td><?php echo $current_group->ApproximateNumberOfMembers;?></td></tr>
			<tr><td valign="top" class="header">Eldplats</td><td><?php echo ja_nej($current_group->NeedFireplace);?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($larp_group->HousingRequestId)->Name;?></td></tr>
		</table>		
		
		
		<h2>Anmälda medlemmar</h2>
		<?php 

		foreach($group_members as $group_member) {

		    echo $group_member->Name . " - " . 
                 $group_member->Profession . " spelas av " . 
                 $group_member->getPerson()->Name . "<br>"; 
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
		

	</div>


</body>
</html>
