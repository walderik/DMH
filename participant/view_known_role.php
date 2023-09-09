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
$person = $role->getPerson();

if ($role->CampaignId != $current_larp->CampaignId) {
    header('Location: index.php'); //Inte den här kampanjen
    exit;
}

if (!$role->isRegistered($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

if (isset($role->GroupId)) {
    $group=Group::loadById($role->GroupId);
}



include 'navigation.php';
?>

	<div class="content">
		<h1><?php echo $role->Name;?>
		</h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Spelas av</td><td>
			
			<?php if ($person->hasPermissionShowName()) echo $person->Name; else echo "(Vill inte visa sitt namn)";?></td>
		<?php 
		if ($role->hasImage()) {
		    
		    $image = Image::loadById($role->ImageId);
		    echo "<td rowspan='20' valign='top'>";
		    echo "<img width='300' src='image.php?id=$role->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</td>";
		}
		?>
			
			</tr>
		<?php if (isset($group)) {?>
			<tr><td valign="top" class="header">Grupp</td><td><a href ="view_known_group.php?id=<?php echo $group->Id;?>"><?php echo $group->Name; ?></a></td></tr>
		<?php }?>
			<tr><td valign="top" class="header">Beskrivning</td><td><?php echo nl2br($role->DescriptionForOthers);?></td></tr>
		</table>		
		</div>


</body>
</html>
