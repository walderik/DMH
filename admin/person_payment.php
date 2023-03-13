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

if (!$person->isRegistered($current_larp)) {
    header('Location: index.php'); //Rollen är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


include 'navigation_subpage.php';
?>


	<div class="content">
		<h1>Hantera betalning för <?php echo $person->Name;?></h1>
		<form action="logic/person_payment_save.php" method="post">
    		<input type="hidden" id="RegistrationId" name="RegistrationId" value="<?php echo $registration->Id; ?>">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
		<table>
			<tr><td valign="top" class="header">Namn</td><td><?php echo $person->Name;?></td></tr>
			<tr><td valign="top" class="header">Personnummer</td><td><?php echo $person->SocialSecurityNumber;?></td></tr>
			<tr><td valign="top" class="header">Email</td><td><?php echo $person->Email ." ".contactEmailIcon($person->Name,$person->Email);?></td></tr>
			<tr><td valign="top" class="header">Mobilnummer</td><td><?php echo $person->PhoneNumber;?></td></tr>
		    <?php 
		    if ($person->getAgeAtLarp($current_larp) < $current_larp->getCampaign()->MinimumAgeWithoutGuardian) {
		    ?>
			<tr><td valign="top" class="header">Ansvarig vuxen</td><td><?php echo $registration->Guardian;?></td></tr>
		    
		    <?php 
		    }
		    ?>
			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><input type="text" id="AmountToPay" name="AmountToPay" value="<?php echo $registration->AmountToPay; ?>"  size="10" maxlength="250"> SEK</td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><input type="text" id="AmountPayed" name="AmountPayed" value="<?php echo $registration->AmountPayed; ?>"  size="10" maxlength="250"> SEK</td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><input type="text" id="Payed" name="Payed" value="<?php echo $registration->Payed; ?>"  size="15" maxlength="250"></td></tr>

			<tr><td valign="top" class="header">Avbokad</td>
			<td>
    			<input type="radio" id="NotComing_yes" name="NotComing" value="1" <?php if ($registration->NotComing == 1) echo 'checked="checked"'?>> 
    			<label for="NeedFireplace_yes">Ja</label><br> 
    			<input type="radio" id="NotComing_no" name="NotComing" value="0" <?php if ($registration->NotComing == 0) echo 'checked="checked"'?>> 
    			<label for="NeedFireplace_no">Nej</label>
			</td></tr>
			<tr><td valign="top" class="header">Anledning till avbokning</td><td><input type="text" id="NotComingReason" name="NotComingReason" value="<?php echo $registration->NotComingReason; ?>"  size="100" maxlength="250"></td></tr>
			
			<tr><td valign="top" class="header">Återbetalning</td><td><input type="text" id="ToBeRefunded" name="ToBeRefunded" value="<?php echo $registration->ToBeRefunded; ?>"  size="10" maxlength="250"> SEK</td></tr>
			<tr><td valign="top" class="header">Återbetalningsdatum</td><td><input type="text" id="RefundDate" name="RefundDate" value="<?php echo $registration->RefundDate; ?>"  size="15" maxlength="250"></td></tr>

		</table>		
			<input type="submit" value="Spara">
		</form>		    