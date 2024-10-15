<?php

include_once 'header.php';

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

$oldGroupCopy = $group->getOldApprovedGroup();

function setClass($text1, $text2, $side) {
    if ($text1 == $text2) {
        if ($side == 1) echo "class='unchangedNew'";
        else echo "class='unchangedOld'";
    } else {
        if ($side == 1) echo "class='changedNew'";
        else echo "class='changedOld'";
    }
}


include 'navigation.php';
?>

<style>
.unchangedNew {
}

.changedNew {

    background-color:#c4fed6;
}

.unchangedOld {
    color:grey;
}

.changedOld {
    background-color:#fbd3cb;
}

</style>

	<div class="content">
		<h1><?php echo $group->Name;?>
		</h1>
		
		<?php 
		if ($group->isApproved()) {
		  echo "<strong>Godkänd</strong>";
		  if (!empty($group->ApprovedByUserId) && !empty($group->ApprovedDate)) {
		      $approvedUser = User::loadById($group->ApprovedByUserId);
		      echo " av $approvedUser->Name, ".substr($group->ApprovedDate,0, 10); 
		  }
		  $editButton = "Ta bort godkännandet";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		    $editButton = "Godkänn";
		}		
		?>
        <form action="logic/toggle_approve_role.php" method="post"><input type="hidden" id="groupId" name="groupId" value="<?php echo $group->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
		<br>
		
 		<div>
 		
		<table>
			<tr>
				<th></th>
				<th>Ny version av gruppen</th>
				<th>Gammal version av gruppen<br>
						  
    		  <?php if (!empty($oldGroupCopy->ApprovedByUserId) && !empty($oldGroupCopy->ApprovedDate)) {
    		      $approvedUser = User::loadById($oldGroupCopy->ApprovedByUserId);
    		      echo "Godkänd av $approvedUser->Name, ".substr($oldGroupCopy->ApprovedDate,0, 10); 
    		  }
    		  ?>
				</th>
			</tr>

			<tr>
				<td valign="top" class="header">Beskrivning</td>
				<td <?php setClass($group->Description, $oldGroupCopy->Description, 1); ?>><?php echo nl2br($group->Description);?></td>
				<td <?php setClass($group->Description, $oldGroupCopy->Description, 2); ?>><?php echo nl2br($oldGroupCopy->Description);?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning för andra</td>
				<td <?php setClass($group->DescriptionForOthers, $oldGroupCopy->DescriptionForOthers, 1); ?>><?php echo nl2br($group->DescriptionForOthers);?></td>
				<td <?php setClass($group->DescriptionForOthers, $oldGroupCopy->DescriptionForOthers, 2); ?>><?php echo nl2br($oldGroupCopy->DescriptionForOthers);?></td>
			</tr>

			<tr>
				<td valign="top" class="header">Vänner</td>
				<td <?php setClass($group->Friends, $oldGroupCopy->Friends, 1); ?>><?php echo nl2br($group->Friends);?></td>
				<td <?php setClass($group->Friends, $oldGroupCopy->Friends, 2); ?>><?php echo nl2br($oldGroupCopy->Friends);?></td>
			</tr>
			<tr>
				<td valign="top" class="header">Fiender</td>
				<td <?php setClass($group->Enemies, $oldGroupCopy->Enemies, 1); ?>><?php echo nl2br($group->Enemies);?></td>
				<td <?php setClass($group->Enemies, $oldGroupCopy->Enemies, 2); ?>><?php echo nl2br($oldGroupCopy->Enemies);?></td>
			</tr>

			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Rikedom</td>
				<td <?php setClass($group->WealthId, $oldGroupCopy->WealthId, 1); ?>>
    			<?php 
    			$wealth = $group->getWealth();
    			if (!empty($wealth)) echo $wealth->Name;
    			?>
    			</td>
				<td <?php setClass($group->WealthId, $oldGroupCopy->WealthId, 2); ?>>
    			<?php 
    			$wealth = $oldGroupCopy->getWealth();
    			if (!empty($wealth)) echo $wealth->Name;
    			?>
    			</td>
			</tr>
			<?php }?>

			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Var bor karaktären?</td>
				<td <?php setClass($group->PlaceOfResidenceId, $oldGroupCopy->PlaceOfResidenceId, 1); ?>>
    			<?php 
    			$por = $group->getPlaceOfResidence();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
				<td <?php setClass($group->PlaceOfResidenceId, $oldGroupCopy->PlaceOfResidenceId, 2); ?>>
    			<?php 
    			$por = $oldGroupCopy->getPlaceOfResidence();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
			</tr>
			<?php } ?>

			<?php if (GroupType::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Typ av grupp</td>
				<td <?php setClass($group->GroupTypeId, $oldGroupCopy->GroupTypeId, 1); ?>>
    			<?php 
    			$por = $group->getGroupType();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
				<td <?php setClass($group->GroupTypeId, $oldGroupCopy->GroupTypeId, 2); ?>>
    			<?php 
    			$por = $oldGroupCopy->getGroupType();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
			</tr>
			<?php } ?>

			<?php if (ShipType::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Typ av shepp</td>
				<td <?php setClass($group->ShipTypeId, $oldGroupCopy->ShipTypeId, 1); ?>>
    			<?php 
    			$por = $group->getShipType();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
				<td <?php setClass($group->ShipTypeId, $oldGroupCopy->ShipTypeId, 2); ?>>
    			<?php 
    			$por = $oldGroupCopy->getShipType();
    			if (!empty($por)) echo $por->Name;
    			?>
    			</td>
			</tr>
			<?php } ?>



			<?php if ($current_larp->getCampaign()->is_me()) { ?>
			<tr>
				<td valign="top" class="header">Färg</td>
				<td <?php setClass($group->Colour, $oldGroupCopy->Colour, 1); ?>><?php echo nl2br($group->Colour);?></td>
				<td <?php setClass($group->Colour, $oldGroupCopy->Colour, 2); ?>><?php echo nl2br($oldGroupCopy->Colour);?></td>
			</tr>
			<?php }?>


			<?php if (IntrigueType::isInUse($current_larp)) {
			    $newIntrigues = commaStringFromArrayObject($group->getIntrigueTypes());
			    $oldIntrigues = commaStringFromArrayObject($oldGroupCopy->getIntrigueTypes());
			    ?>
			<tr>
				<td valign="top" class="header">Intrigtyper</td>
				<td <?php setClass($newIntrigues, $oldIntrigues, 1); ?>><?php echo $newIntrigues;?></td>
				<td <?php setClass($newIntrigues, $oldIntrigues, 2); ?>><?php echo $oldIntrigues;?></td>
			</tr>
			<?php } ?>

			<tr>
				<td valign="top" class="header">Intrigidéer</td>
				<td <?php setClass($group->IntrigueIdeas, $oldGroupCopy->IntrigueIdeas, 1); ?>><?php echo nl2br($group->IntrigueIdeas); ?></td>
				<td <?php setClass($group->IntrigueIdeas, $oldGroupCopy->IntrigueIdeas, 2); ?>><?php echo nl2br($oldGroupCopy->IntrigueIdeas); ?></td>
			</tr>


		</table>		
		</div>
		
		
		<h2>Anteckningar (visas inte för deltagaren) <a href='edit_group_intrigue.php?id=<?php echo $group->Id ?>'><i class='fa-solid fa-pen'></i></a></h2>
		<div>
		<?php    echo nl2br(htmlspecialchars($group->OrganizerNotes)); ?>
		</div>
		<?php include 'print_group_history.php';?>	

	</div>


</body>
</html>
