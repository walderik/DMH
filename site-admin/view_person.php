<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $person = Person::loadById($_GET['id']);
    } else {
        header('Location: index.php');
        exit;
    }
}



include 'navigation.php';

$is_member = false;
$houses = $person->housesOf();
foreach ($houses as $house) {
    $houseCareTaker = $house->getHousecaretakerForPerson($person);
    if (!empty($houseCareTaker)) {
        $is_member = $houseCareTaker->isMember();
        break;
    }
}

?>

	<div class="content">
		<h1><?php echo $person->Name;?>&nbsp;<a href='edit_person.php?id=<?php echo $person->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Personnummer</td><td><?php echo $person->SocialSecurityNumber;?></td></tr>
						<tr><td valign="top" class="header">Medlem i Berghems vänner just nu</td><td><?php echo showStatusIcon($is_member)?></td></tr>
			<tr><td valign="top" class="header">Email</td><td><?php echo $person->Email." ".contactEmailIcon($person, BerghemMailer::ASSOCIATION);?></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td><td><?php echo $person->PhoneNumber;?></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig</td><td><?php echo $person->EmergencyContact;?></td></tr>
			<tr><td valign="top" class="header">Erfarenhet</td><td><?php echo Experience::loadById($person->ExperienceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Intriger du inte vill spela på</td><td><?php echo $person->NotAcceptableIntrigues;?></td></tr>

			<tr><td valign="top" class="header">Vanliga allergier</td><td><?php echo commaStringFromArrayObject($person->getNormalAllergyTypes());?></td></tr>

			<tr><td valign="top" class="header">Andra allergier</td><td><?php echo $person->FoodAllergiesOther;?></td></tr>

			<tr>
				<td valign="top" class="header">Husförvaltare</td>
				<td><?php 
				
				  $houseslinks = array();
				  foreach ($houses as $house) {
				      $houseslinks[] = "<a href='view_house.php?id=$house->Id'>".$house->Name."</a>";
				  }
				  echo implode("<br />", $houseslinks);
				  ?>
			</tr>
			
			<tr><td valign="top" class="header">Hälsa</td><td><?php echo $person->HealthComment;?></td></tr>


			<tr><td valign="top" class="header">Annan information</td><td><?php echo nl2br($person->OtherInformation);?></td></tr>
			<tr><td valign="top" class="header">Får visa namn</td><td><?php echo ja_nej($person->HasPermissionShowName)?></td></tr>
		</table>	
		</div>	
	</div>


</body>
</html>
