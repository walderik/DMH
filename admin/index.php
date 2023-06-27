<?php
require 'header.php';
include_once '../includes/error_handling.php';

include "navigation.php";
?>

		<div class="content">
			<h1>Administration av <?php echo $current_larp->Name;?></h1>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
	  <?php $payment_array = PaymentInformation::allBySelectedLARP($current_larp);
        if (empty($payment_array)) {
      ?>
            <div class="content">Inga <a href="payment_information_admin.php">deltagaravgifter</a> är satta. Gör det innan anmälan öppnar.</div>
        <?php
        }
        ?>
                <div class="content">
        
        
            Anmälan är 
            <?php if ($current_larp->RegistrationOpen == 1) {
                echo "öppen";
                $openButton = "Stäng";
            }
            else {
                echo "stängd";
                $openButton = "Öppna";
            }
                  
                ?>
            <form action="logic/toggle_larp_registration_open.php"><input type="submit" value="<?php echo $openButton;?>"></form>

        </div>
		<div class="content">
			Just nu är det <?php echo Registration::countAllNonOfficials($current_larp); ?> anmälda deltagare och <?php echo Registration::countAllOfficials($current_larp); ?> funktionärer.<br> 
        <?php 

            if ($current_larp->isFull() || Reserve_Registration::isInUse($current_larp)) {
                echo "<br>Lajvet är fullt nu.".
                "<br>Just nu är det ". Reserve_Registration::count($current_larp) . " st på reservlistan.";
                
            }
            
            ?>
                

		</div>
		<?php 
		$approval_count = count (Person::getAllToApprove($current_larp));
		if ($approval_count>0) { ?>
			<div class="content">
			<?php echo $approval_count; ?> deltagare har karaktärer som väntar på <a href="persons_to_approve.php">godkännande</a>. 
		
			</div>			
		<?php }
		
		$approval_t_count = count (Telegram::getAllToApprove($current_larp));
		if ($approval_t_count>0) { ?>
			<div class="content">
			<?php echo $approval_t_count; ?> telegram som väntar på <a href="telegram_admin.php">godkännande</a>. 
		
			</div>			
		<?php }?>
		
		<div class="content">
            <a href="not_registered_roles.php">Karaktärer som inte är anmälda (än) i år</a>
		</div>
		
		<div class="content">
			Förväntade intäkter: <?php echo Registration::totalIncomeToBe($current_larp);?> SEK<br>
		    Faktiskta intäkter: <?php echo Registration::totalIncomeToday($current_larp)?> SEK
		</div>
		<div class="content">
		<a href="doh_ssn_check.php">Medlemskontroll flera personnummer.</a> 
		</div>

	</body>
</html>