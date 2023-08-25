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
    header('Location: index.php'); // karaktären är inte anmäld
    exit;
}

$registration = Registration::loadByIds($person->Id, $current_larp->Id);

if (isset($_SERVER['HTTP_REFERER'])) {
    $referer = $_SERVER['HTTP_REFERER'];
}
else {
    $referer = "";
}


include 'navigation.php';
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
			<tr><td valign="top" class="header">Ansvarig vuxen</td><td>
			<?php if (!empty($registration->GuardianId)) echo $registration->getGuardian()->Name; else echo showStatusIcon(false);?></td></tr>

		    
		    <?php 
		    }
		    ?>
			<tr><td valign="top" class="header">Betalningsreferens</td><td><?php echo $registration->PaymentReference;?></td></tr>
			<tr><td valign="top" class="header">Belopp att betala</td><td><input type="number" id="AmountToPay" name="AmountToPay" value="<?php echo $registration->AmountToPay; ?>"  min="0" size="10" maxlength="250"> SEK</td></tr>
			<tr><td valign="top" class="header">Belopp betalat</td><td><input type="number" id="AmountPayed" name="AmountPayed" value="<?php echo $registration->AmountPayed; ?>"  min="0" size="10" maxlength="250"> SEK</td></tr>
			<tr><td valign="top" class="header">Betalat datum</td><td><input type="date" id="Payed" name="Payed" value="<?php echo $registration->Payed; ?>"  size="15" maxlength="250"> <a href='economy_receipt_pdf.php?registrationId=<?php echo $registration->Id?>' target='_blank'><i class='fa-solid fa-file-pdf' title='Skapa kvitto'></i></a>
			</td></tr>
			
			<tr><td valign="top" class="header">Avbokad</td>
			<td>
    			<input type="radio" id="NotComing_yes" name="NotComing" value="1" <?php if ($registration->NotComing == 1) echo 'checked="checked"'?>> 
    			<label for="NotComing_yes">Ja</label><br> 
    			<input type="radio" id="NotComing_no" name="NotComing" value="0" <?php if ($registration->NotComing == 0) echo 'checked="checked"'?>> 
    			<label for="NotComing_no">Nej</label>
			</td></tr>
			<tr><td valign="top" class="header">Anledning till avbokning</td><td><input type="text" id="NotComingReason" name="NotComingReason" value="<?php echo htmlspecialchars($registration->NotComingReason); ?>"  size="100" maxlength="250"></td></tr>
			
			<tr><td valign="top" class="header">Ska ha återbetalning</td>
			<td>
    			<input type="radio" id="IsToBeRefunded_yes" name="IsToBeRefunded" value="1" <?php if ($registration->IsToBeRefunded == 1) echo 'checked="checked"'?>> 
    			<label for="IsToBeRefunded_yes">Ja</label><br> 
    			<input type="radio" id="IsToBeRefunded_no" name="IsToBeRefunded" value="0" <?php if ($registration->IsToBeRefunded == 0) echo 'checked="checked"'?>> 
    			<label for="IsToBeRefunded_no">Nej</label>
			</td></tr>
			<tr><td valign="top" class="header">Belopp återbetalat</td><td><input type="number" id="RefundAmount" name="RefundAmount" value="<?php echo $registration->RefundAmount; ?>"  min="0" size="10" maxlength="250"> SEK</td></tr>
			<tr><td valign="top" class="header">Återbetalningsdatum</td><td><input type="date" id="RefundDate" name="RefundDate" value="<?php echo $registration->RefundDate; ?>"  size="15" maxlength="250"></td></tr>

		</table>		
			<input type="submit" value="Spara">
		</form>		    