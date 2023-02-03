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



function ja_nej($val) {
    if ($val == 0) return "Nej";
    if ($val == 1) return "Ja";
}

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


			<tr><td valign="top" class="header">Annan information</td><td><?php echo $current_person->OtherInformation;?></td></tr>

		</table>		
		<?php 
		
		//TODO husförvaltare, medlem
		/*
		public $Approved; //Date
		public $RegisteredAt;
		public $PaymentReference;
		public $AmountToPay;
		public $AmountPayed = 0;
		public $Payed; //Datum
		public $IsMember;
		public $MembershipCheckedAt;
		public $NotComing = 1;
		public $ToBeRefunded;
		public $RefundDate;
		public $IsOfficial = 0;
		public $NPCDesire;
		public $HousingRequestId;
		*/
		?>

	</div>


</body>
</html>
