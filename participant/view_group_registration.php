<?php

include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $GroupId = $_GET['id'];
        $group = Group::loadById($GroupId);
    }
    else {
        header('Location: index.php');
        exit;
    }
}


if (!isset($group) || !$group->isRegistered($current_larp)) {
    header('Location: index.php'); // gruppen är inte anmäld
    exit;
}

if (!$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group)) {
    header('Location: index.php?error=no_member'); //Inte medlem i gruppen
    exit;
}

$larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);


include 'navigation.php';

?>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-file"></i>
			Anmälan för <?php echo $group->Name;?>
		</div>
   		<div class='itemcontainer'>
	   		<div class='itemcontainer'>
           	<div class='itemname'>Vill gruppen ha intriger?</div>
			<?php echo ja_nej($larp_group->WantIntrigue);?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Kvarvarande intriger</div>
			<?php echo nl2br(htmlspecialchars($larp_group->RemainingIntrigues));?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Vad har hänt?</div>
			<?php echo nl2br(htmlspecialchars($larp_group->WhatHappenedSinceLastLarp));?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Antal medlemmar</div>
			<?php echo $larp_group->ApproximateNumberOfMembers;?>
			</div>

			<?php if (HousingRequest::isInUse($current_larp)) { ?>
	   	   		<div class='itemcontainer'>
               	<div class='itemname'>Önskat boende</div>
				<?php 
				$housingrequest = $larp_group->getHousingRequest();
				    if(!empty($housingrequest)) echo $housingrequest->Name;
			    ?>
    			</div>
			<?php } ?>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Behöver ni eldplats?</div>
			<?php echo ja_nej($larp_group->NeedFireplace);?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Typ av tält</div>
			<?php echo nl2br(htmlspecialchars($larp_group->TentType));?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Storlek på tält</div>
			<?php echo nl2br(htmlspecialchars($larp_group->TentSize));?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Vilka ska bo i tältet</div>
			<?php echo nl2br(htmlspecialchars($larp_group->TentHousing));?>
			</div>

	   		<div class='itemcontainer'>
           	<div class='itemname'>Önskad placering</div>
			<?php echo nl2br(htmlspecialchars($larp_group->TentPlace));?>
			</div>

		</div>
	</div>


</body>
</html>
