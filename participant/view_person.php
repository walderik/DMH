<?php

require 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
    }
}

$current_person = Person::loadById($PersonId);

if (Person::loadById($current_person->Id)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din person
}

if (!$current_person->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
}

$registration = Registration::loadByIds($current_person->Id, $current_larp->Id);




?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


	<div class="content">
		<h1><?php echo $current_person->Name;?></h1>
		<table>
			<tr><td valign="top" class="header">Personnummer</td><td><?php echo $current_person->SocialSecurityNumber;?></td></tr>
			<tr><td valign="top" class="header">Email</td><td><?php echo $current_person->Email;?></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td><td><?php echo $current_person->PhoneNumber;?></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig</td><td><?php echo $current_person->EmergencyContact;?></td></tr>

			<tr><td valign="top" class="header">Typ av lajvare</td><td><?php echo LarperType::loadById($current_person->LarperTypeId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Kommentar till typ av lajvare</td><td><?php echo $current_person->TypeOfLarperComment;?></td></tr>


			<tr><td valign="top" class="header">Erfarenhet</td><td><?php echo Experience::loadById($current_person->ExperienceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Intriger du inte vill spela på</td><td><?php echo $current_person->NotAcceptableIntrigues;?></td></tr>

			<tr><td valign="top" class="header">Typ av mat</td><td><?php echo TypeOfFood::loadById($current_person->TypeOfFoodId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Vanliga allergier</td><td><?php echo commaStringFromArrayObject($current_person->getNormalAllergyTypes());?></td></tr>

			<tr><td valign="top" class="header">Andra allergier</td><td><?php echo $current_person->FoodAllergiesOther;?></td></tr>

			<tr><td valign="top" class="header">NPC önskemål</td><td><?php echo $registration->NPCDesire;?></td></tr>
			<tr><td valign="top" class="header">Husförvaltare</td><td><?php if (isset($current_person->HouseId)) { echo $current_person->getHouse()->Name; }?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($registration->HousingRequestId)->Name;?></td></tr>


			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_person->OtherInformation;?></td></tr>
			<tr><td valign="top" class="header">Medlem</td><td><?php echo ja_nej($current_person->isMember($current_larp->StartDate))?></td></tr>
			<tr><td valign="top" class="header">Anmäld</td><td><?php echo $registration->RegisteredAt;?></td></tr>
			<tr><td valign="top" class="header">Godkänd</td><td><?php if (isset($registration->Approved)) { echo $registration->Approved; } else { echo "Nej"; }?></td></tr>
			<tr><td valign="top" class="header">Funktionär</td><td><?php echo ja_nej($registration->IsOfficial)?></td></tr>

			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><?php echo $registration->AmountToPay;?></td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><?php echo $registration->AmountPayed;?></td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><?php echo $registration->Payed;?></td></tr>
			<?php 
			if ($registration->NotComing) {
			?>
			<tr><td valign="top" class="header">Avbokad</td><td><?php echo ja_nej($registration->NotComing);?></td></tr>
			<tr><td valign="top" class="header">Återbetalning</td><td><?php echo $registration->ToBeRefunded;?></td></tr>
			<tr><td valign="top" class="header">Återbetalningsdatum</td><td><?php echo $registration->RefundDate;?></td></tr>
			<?php  }?>
		</table>		


	</div>


</body>
</html>
