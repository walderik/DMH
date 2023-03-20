<?php

include_once 'header.php';


$group = Group::newWithDefault();
$larp_group = LARP_Group::newWithDefault();
$persons_in_group = Person::getPersonsForUser($current_user->Id);
    



if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}

include 'navigation_subpage.php';

?>


	<div class="content">
		<h1><?php echo $group->Name;?></h1>
		<form action="logic/create_group_save.php" method="post">
    		<input type="hidden" id="GroupId" name="GroupId" value="<?php echo $group->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr><td valign="top" class="header">Namn</td>
			<td><input type="text" id="Name" name="Name" value="<?php echo $group->Name; ?>" required></td></tr>

			<tr><td valign="top" class="header">Gruppansvarig</td>
			<td><?php selectionByArray('Person', $persons_in_group, false, true, $group->PersonId); ?>
			Byt till rätt gruppledare när du har lagt in medlemmar i gruppen.</td></tr>

			<tr><td valign="top" class="header">Beskrivning</td>
			<td><textarea id="Description" name="Description" rows="4" cols="50" maxlength="60000" required><?php echo $group->Description; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Vänner</td>
			<td><textarea id="Friends" name="Friends" rows="4" cols="50" maxlength="60000"><?php echo $group->Friends; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Fiender</td>
			<td><textarea id="Enemies" name="Enemies" rows="4" cols="50" maxlength="60000"><?php echo $group->Enemies; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Rikedom</td>
			<td><?php Wealth::selectionDropdown(false, true, $group->WealthId);?></td></tr>

			<tr><td valign="top" class="header">Var bor gruppen?</td>
			<td><?php PlaceOfResidence::selectionDropdown(false, true, $group->PlaceOfResidenceId);?></td></tr>

			<tr><td valign="top" class="header">Intrig</td>
			<td>
				<input type="radio" id="WantIntrigue_yes" name="WantIntrigue" value="1" <?php if ($larp_group->WantIntrigue == 1) echo 'checked="checked"'?>> 
    			<label for="WantIntrigue_yes">Ja</label><br> 
    			<input type="radio" id="WantIntrigue_no" name="WantIntrigue" value="0" <?php if ($larp_group->WantIntrigue == 0) echo 'checked="checked"'?>> 
    			<label for="WantIntrigue_no">Nej</label>
			</td></tr>

			<tr><td valign="top" class="header">Intrigtyper</td>
			<td><?php IntrigueType::selectionDropdown(true, false, $larp_group->getSelectedIntrigueTypeIds());?></td></tr>

			<tr><td valign="top" class="header">Intrigidéer</td>
			<td><textarea id="IntrigueIdeas" name="IntrigueIdeas" rows="4" cols="50" maxlength="60000"><?php echo $group->IntrigueIdeas; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Kvarvarande intriger</td>
			<td><textarea id="RemainingIntrigues" name="RemainingIntrigues" rows="4" cols="50" maxlength="60000"><?php echo $larp_group->RemainingIntrigues; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Annan information</td>
			<td><textarea id="OtherInformation" name="OtherInformation" rows="4" cols="50" maxlength="60000"><?php echo $group->OtherInformation; ?></textarea></td></tr>

			<tr><td valign="top" class="header">Antal medlemmar</td>
			<td><input type="text" id="ApproximateNumberOfMembers" name="ApproximateNumberOfMembers" value="<?php echo $larp_group->ApproximateNumberOfMembers; ?>" required></td></tr>

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
		
		

	</div>


</body>
</html>
