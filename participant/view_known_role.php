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


	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-person"></i>
			<?php echo $role->Name;?>
		</div>

		<?php 
		if ($role->hasImage()) {
		    echo "<div class='itemcontainer'>";
		    $image = Image::loadById($role->ImageId);
		    echo "<img width='300' src='../includes/display_image.php?id=$role->ImageId'/>\n";
		    if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
		    echo "</div>";
		}
		?>


   		<div class='itemcontainer'>
       	<div class='itemname'>Spelas av</div>
		<?php if ($person->hasPermissionShowName()) echo $person->Name; else echo "(Vill inte visa sitt namn)";?>
		</div>

		<?php if (isset($group)) {?>
       		<div class='itemcontainer'>
           	<div class='itemname'>Grupp</div>
    		<a href ="view_known_group.php?id=<?php echo $group->Id;?>"><?php echo $group->Name; ?></a>
    		</div>
		<?php }?>

   		<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning</div>
		<?php echo nl2br($role->DescriptionForOthers);?>
		</div>
	</div>
</body>
</html>
