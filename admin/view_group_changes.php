<?php

include_once 'header.php';
include '../includes/finediff.php';

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

if (empty($oldGroupCopy)) {
    header('Location: view_group.php?id='.$group->Id.'.php');
    exit;
}

function echoDiff($fromtxt, $totxt) {
    $opcodes = FineDiff::getDiffOpcodes($fromtxt, $totxt, FineDiff::$wordGranularity);
    $to_text = FineDiff::renderDiffToHTMLFromOpcodes($fromtxt, $opcodes);
    
    echo "<td>$to_text</td>";
    
}

function echoNameDiff($oldthing, $newthing) {
    $oldName = "";
    $newName = "";
    
    
    if (!empty($newthing)) $newName = $newthing->Name;
    if (!empty($oldthing)) $oldName = $oldthing->Name;
    
    echoDiff($oldName, $newName);
}




include 'navigation.php';
?>

<style>
ins {
	color: green;
	background: #dfd;
	text-decoration: none
}

del {
	color: red;
	background: #fdd;
	text-decoration: none
}
</style>

	<div class="content">
		<h1><?php echo $group->Name;?>
		</h1>
		
		<?php 
		if ($group->isApproved()) {
		  echo "<strong>Godkänd</strong>";
		  if (!empty($group->ApprovedByPersonId) && !empty($group->ApprovedDate)) {
		      $approver = Person::loadById($group->ApprovedByPersonId);
		      echo " av $approver->Name, ".substr($group->ApprovedDate,0, 10); 
		  }
		  $editButton = "Ta bort godkännandet";
		}
		else {
		    echo "<strong>Ej godkänd</strong>";
		    $editButton = "Godkänn";
		}		
		?>
        <form action="logic/toggle_approve_role.php" method="post"><input type="hidden" id="groupId" name="groupId" value="<?php echo $group->Id;?>"><input type="submit" value="<?php echo $editButton;?>"></form>
	  	<?php if (!empty($oldGroupCopy->ApprovedByPersonId) && !empty($oldGroupCopy->ApprovedDate)) {
	  	    $approver = Person::loadById($oldGroupCopy->ApprovedByPersonId);
		      echo "Tidigare godkänd av $approver->Name, ".substr($oldGroupCopy->ApprovedDate,0, 10); 
		  }
	  ?>
		
		<br>
		
 		<div>
 		
		<table>
			<tr>
				<td valign="top" class="header">Beskrivning</td>
				<?php echoDiff($oldGroupCopy->Description, $group->Description); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Beskrivning för andra</td>
				<?php echoDiff($oldGroupCopy->DescriptionForOthers, $group->DescriptionForOthers); ?>
			</tr>

			<tr>
				<td valign="top" class="header">Vänner</td>
				<?php echoDiff($oldGroupCopy->Friends, $group->Friends); ?>
			</tr>
			<tr>
				<td valign="top" class="header">Fiender</td>
				<?php echoDiff($oldGroupCopy->Enemies, $group->Enemies); ?>
			</tr>

			<?php if (Wealth::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Rikedom</td>
				<?php echoNameDiff($oldGroupCopy->getWealth(), $group->getWealth()); ?>
			</tr>
			<?php }?>

			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Var bor karaktären?</td>
				<?php echoNameDiff($oldGroupCopy->getPlaceOfResidence(), $group->getPlaceOfResidence()); ?>
			</tr>
			<?php } ?>

			<?php if (GroupType::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Typ av grupp</td>
				<?php echoNameDiff($oldGroupCopy->getGroupType(), $group->getGroupType()); ?>
			</tr>
			<?php } ?>

			<?php if (ShipType::isInUse($current_larp)) {?>
			<tr>
				<td valign="top" class="header">Typ av skepp</td>
				<?php echoNameDiff($oldGroupCopy->getShipType(), $group->getShipType()); ?>
			</tr>
			<?php } ?>



			<?php if ($current_larp->getCampaign()->is_me()) { ?>
			<tr>
				<td valign="top" class="header">Färg</td>
				<?php echoDiff($oldGroupCopy->Colour, $group->Colour); ?>
			</tr>
			<?php }?>

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
