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
if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}


$houses = $person->housesOf();

?>

	<div class="content">
		<h1><?php echo $person->Name;?>&nbsp;<a href='edit_person.php?id=<?php echo $person->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Personnummer</td><td><?php echo $person->SocialSecurityNumber;?></td></tr>
			<tr><td valign="top" class="header">Medlem i Berghems vänner just nu</td><td><?php echo showStatusIcon($person->IsMember())?></td></tr>
			<tr><td valign="top" class="header">Email</td><td><?php echo $person->Email." ".contactEmailIcon($person, BerghemMailer::ASSOCIATION);?></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td><td><?php echo $person->PhoneNumber;?></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig</td><td><?php echo $person->EmergencyContact;?></td></tr>
			<tr><td valign="top" class="header">Erfarenhet</td><td><?php echo Experience::loadById($person->ExperienceId)->Name;?></td></tr>
			<tr>
				<td valign="top" class="header">Husförvaltare</td>
				<td><?php 
				
				  $houseslinks = array();
				  foreach ($houses as $house) {
				      $link = "<a href='view_house.php?id=$house->Id'>".$house->Name."</a> ";
				      $link .= remove_housecaretaker($person,  $house);
                      $houseslinks[] = $link;
				  }
				  echo implode("<br />", $houseslinks);
				  ?>

				</td>
			</tr>
		</table>	
		</div>	
	</div>


</body>
</html>
