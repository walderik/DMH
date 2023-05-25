<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        $PersonId = $_GET['id'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$person = Person::loadById($PersonId);

if (!$person->isRegistered($current_larp) && !$person->isReserve($current_larp)) {
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);


include 'navigation.php';

?>

	<div class="content">
		<h1><?php echo $person->Name;?>&nbsp;<a href='edit_person.php?id=<?php echo $person->Id;?>'><i class='fa-solid fa-pen'></i></a></h1>
		<div>
		<table>
			<tr><td valign="top" class="header">Personnummer</td><td><?php echo $person->SocialSecurityNumber;?></td></tr>
			<tr><td valign="top" class="header">Email</td><td><?php echo $person->Email." ".contactEmailIcon($person->Name,$person->Email);?></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td><td><?php echo $person->PhoneNumber;?></td></tr>
			<tr><td valign="top" class="header">Närmaste anhörig</td><td><?php echo $person->EmergencyContact;?></td></tr>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<tr><td valign="top" class="header">Ansvarig vuxen</td><td>
			
			<?php if (!empty($registration->GuardianId)) echo "<a href='view_person.php?id=$registration->GuardianId'>".$registration->getGuardian()->Name."</a>"; else echo showStatusIcon(false); ?>
			
			</td></tr>
		    
		    <?php 
		    }
		    ?>


			<tr><td valign="top" class="header">Erfarenhet</td><td><?php echo Experience::loadById($person->ExperienceId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Intriger du inte vill spela på</td><td><?php echo $person->NotAcceptableIntrigues;?></td></tr>

			<tr><td valign="top" class="header">Typ av mat</td><td><?php echo TypeOfFood::loadById($registration->TypeOfFoodId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Vanliga allergier</td><td><?php echo commaStringFromArrayObject($person->getNormalAllergyTypes());?></td></tr>

			<tr><td valign="top" class="header">Andra allergier</td><td><?php echo $person->FoodAllergiesOther;?></td></tr>

			<tr><td valign="top" class="header">NPC önskemål</td><td><?php echo $registration->NPCDesire;?></td></tr>
			<tr><td valign="top" class="header">Husförvaltare</td><td><?php if (isset($person->HouseId)) { echo $person->getHouse()->Name; }?></td></tr>
			<tr><td valign="top" class="header">Önskat boende</td><td><?php echo HousingRequest::loadById($registration->HousingRequestId)->Name;?></td></tr>
			<tr><td valign="top" class="header">Boendehänsyn</td><td><?php echo $person->HousingComment;?></td></tr>
			<tr><td valign="top" class="header">Hälsa</td><td><?php echo $person->HealthComment;?></td></tr>


			<tr><td valign="top" class="header">Annan information</td><td><?php echo nl2br($person->OtherInformation);?></td></tr>
			<tr><td valign="top" class="header">Medlem</td><td><?php echo ja_nej($registration->isMember())?></td></tr>
			<tr><td valign="top" class="header">Anmäld</td><td><?php echo $registration->RegisteredAt;?></td></tr>
			<tr><td valign="top" class="header">Godkänd</td><td><?php if (isset($registration->ApprovedCharacters)) { echo $registration->ApprovedCharacters; } else { echo "Nej"; }?></td></tr>
			<tr><td valign="top" class="header">Funktionär</td><td><?php echo ja_nej($registration->IsOfficial)?></td></tr>
			<tr><td valign="top" class="header">Typ av funktionär</td><td><?php echo commaStringFromArrayObject($registration->getOfficialTypes());?></td></tr>

			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><?php echo $registration->AmountToPay;?></td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><?php echo $registration->AmountPayed;?></td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><?php echo $registration->Payed;?></td></tr>
			<?php 
			if ($registration->isNotComing()) {
			?>
			<tr><td valign="top" class="header">Avbokad</td><td><?php echo ja_nej($registration->NotComing);?></td></tr>
			<tr><td valign="top" class="header">Återbetalning</td><td><?php echo $registration->ToBeRefunded;?></td></tr>
			<tr><td valign="top" class="header">Återbetalningsdatum</td><td><?php echo $registration->RefundDate;?></td></tr>
			<?php  }?>
		</table>	
		</div>	
		<?php 
    		        echo "<div>\n";
    		        echo "<strong>Karaktärer:</strong><br>\n";
    		        $roles = $person->getRolesAtLarp($current_larp);
    		        foreach($roles as $role) {

		                $role_group = $role->getGroup();
		                $role_group_name = "";
		                if (isset($role_group)) {
		                    $role_group_name = " - $role_group->Name";
		                }
		                echo $role->Name . " - " . $role->Profession . " " . $role_group_name;
    		            if (LARP_Role::loadByIds($role->Id, $current_larp->Id)->IsMainRole != 1) {
    		              echo " Sidokaraktär";
    		            }
    		            echo "&nbsp;<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
    		            echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a><br>\n";
		            
    		            
    		        }
    		        echo "<br>";
    		        echo "<form action='change_main_role.php' method='post'>";
    		        echo "<input type='hidden' id='PersonId' name='PersonId' value='$person->Id'>";
    		        echo "<input type='submit' value='Ändra vilken som är huvudkaraktär'>";
    		        echo "</div>\n";
    		        
    		        ?>
	</div>


</body>
</html>
