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
            }
            else {
                echo "stängd";
            }
                  
                ?>

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
					<div class="content">
		<?php 
		$approval_count_group = count (Group::getAllToApprove($current_larp));
		$approval_count_roles = count (Role::getAllToApprove($current_larp));
		if ($approval_count_group>0 || $approval_count_roles>0) echo "$approval_count_group grupper och $approval_count_roles karaktärer väntar på <a href='approval.php'>godkännande</a>.<br>"; 

		$approval_t_count = count (Telegram::getAllToApprove($current_larp));
		if ($approval_t_count>0) echo "$approval_t_count telegram väntar på <a href='telegram_admin.php'>godkännande</a>. <br>";

		$approval_l_count = count (Letter::getAllToApprove($current_larp));
		if ($approval_l_count>0) echo "$approval_l_count brev väntar på <a href='letter_admin.php'>godkännande</a>. <br>";

		$approval_r_count = count (Rumour::getAllToApprove($current_larp));
		if ($approval_r_count>0) echo "$approval_r_count rykten väntar på <a href='rumour_admin.php'>godkännande</a>.<br>"; 
		
		$approval_count = count (Alchemy_Supplier::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count lövjerister har ingredienslistor som väntar på <a href='alchemy_supplier_admin.php'>godkännande</a>.<br>";

		$approval_count = count (Alchemy_Ingredient::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count alkemiska ingredienser väntar på <a href='alchemy_ingredient_admin.php'>godkännande</a>.<br>";
		
		?>
		</div>
		
		<div class="content">
            <a href="not_registered_roles.php">Grupper och karaktärer som inte är anmälda (än) i år</a>
		</div>
		
		<div class="content">
		<table>
			<tr><td style='font-weight: normal'>Förväntade intäkter:</td><td align='right'><?php echo Registration::totalIncomeToBe($current_larp);?> SEK</td></tr>
		    <tr><td style='font-weight: normal'>Faktiskta intäkter:</td><td align='right'><?php echo Registration::totalIncomeToday($current_larp)?> SEK</td></tr>
	    </table>
		</div>

	</body>
</html>