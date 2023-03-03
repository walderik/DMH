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

$group = Group::loadById($GroupId);


if (!$group->isRegistered($current_larp)) {
    header('Location: index.php'); //Gruppen är inte anmäld
}

$larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);

$group_members = Role::getRegisteredRolesInGroup($group, $current_larp);

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


	<div class="content">
		<h1><?php echo $group->Name;?></h1>
		<form action="logic/edit_person_save.php" method="post">
    		<input type="hidden" id="Id" name="Id" value="<?php echo $group->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">

		
    		
    		
		<table>
			<tr><td valign="top" class="header">Namn</td>
			<td><input type="text" id="Name" name="Name" value="<?php echo $group->Name; ?>" required></td></tr>

			<tr><td valign="top" class="header">Gruppansvarig</td>
			<td><a href ="view_person.php?id=<?php echo $group->PersonId;?>"><?php echo $group->getPerson()->Name;?></a></td></tr>

			<tr><td valign="top" class="header">Beskrivning</td>
			<td><textarea id="Description" name="Description" rows="4" cols="50" required><?php echo $group->Description; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Vänner</td>
			<td><textarea id="Friends" name="Friends" rows="4" cols="50"><?php echo $group->Friends; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Fiender</td>
			<td><textarea id="Enemies" name="Enemies" rows="4" cols="50"><?php echo $group->Enemies; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Rikedom</td>
			<td><?php Wealth::selectionDropdown(false, true, $group->WealthId);?></td></tr>

			<tr><td valign="top" class="header">Var bor gruppen?</td>
			<td><?php PlaceOfResidence::selectionDropdown(false, true, $group->PlaceOfResidenceId);?></td></tr>

			<tr><td valign="top" class="header">Intrigtyper</td>
			<td><?php echo commaStringFromArrayObject($larp_group->getIntrigueTypes());?></td></tr>

			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="50"><?php echo $group->OtherInformation; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Intrig</td>
			<td>
				<input type="radio" id="WantIntrigue_yes" name="WantIntrigue" value="1" <?php if ($larp_group->WantIntrigue == 1) echo 'checked="checked"'?>> 
    			<label for="WantIntrigue_yes">Ja</label><br> 
    			<input type="radio" id="WantIntrigue_no" name="WantIntrigue" value="0" <?php if ($larp_group->WantIntrigue == 0) echo 'checked="checked"'?>> 
    			<label for="WantIntrigue_no">Nej</label>
			</td></tr>

			<tr><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="50"><?php echo $group->IntrigueIdeas; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="RemainingIntrigues" name="RemainingIntrigues" rows="4" cols="50"><?php echo $larp_group->RemainingIntrigues; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Antal medlemmar</td>
			<td><input type="text" id="ApproximateNumberOfMembers" name="ApproximateNumberOfMembers" required></td></tr>

			<tr><td valign="top" class="header">Önskat boende</td>
			<td><?php HousingRequest::selectionDropdown(false,true, $larp_group->HousingRequestId);?></td></tr>

			<tr><td valign="top" class="header">Eldplats</td>
			<td>
				<input type="radio" id="NeedFireplace_yes" name="NeedFireplace" value="1" <?php if ($larp_group->NeedFireplace == 1) echo 'checked="checked"'?>> 
    			<label for="NeedFireplace_yes">Ja</label><br> 
    			<input type="radio" id="NeedFireplace_no" name="NeedFireplace" value="0" <?php if ($larp_group->NeedFireplace == 0) echo 'checked="checked"'?>> 
    			<label for="NeedFireplace_no">Nej</label>
			</td></tr>
		</table>		
			<input type="submit" value="Spara">

			</form>
		
		
		<h2>Anmälda medlemmar</h2>
		<?php 

		foreach($group_members as $group_member) {

		    echo "<a href ='view_role.php?id=" . $group_member->Id ."'>" . 
		    $group_member->Name . "</a> - " . 
            $group_member->Profession . " spelas av " . 
            "<a href ='view_person.php?id=" . $group_member->getPerson()->Id . "'>" .
            $group_member->getPerson()->Name . "</a>";


            if ($group_member->getPerson()->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
                echo ", ansvarig vuxen är " . $group_member->getRegistration($current_larp)->Guardian;
		    }

         ?>
		         <a href="remove_group_member.php?groupID=<?php echo $group->Id; ?>&roleID=<?php echo $group_member->Id; ?>" onclick="return confirm('Är du säker på att du vill ta bort karaktären från gruppen?');"><i class="fa-solid fa-trash-can"></i></a>
		<?php 
		    

            echo "<br>"; 
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
