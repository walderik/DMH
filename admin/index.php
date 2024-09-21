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
            <?php echo ($current_larp->RegistrationOpen == 1) ? "öppen" : "stängd"; ?>

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
		
		$approval_count = count (Alchemy_Alchemist::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count alkemister har receptlistor som väntar på <a href='alchemy_alchemist_admin.php'>godkännande</a>.<br>";
		
		$approval_count = count (Alchemy_Recipe::getAllToApprove($current_larp));
		if ($approval_count>0) echo "$approval_count alkemiska recept väntar på <a href='alchemy_recipe_admin.php'>godkännande</a>.<br>";
		
		if ($current_larp->chooseParticipationDates()) {
		    $approval_count = count (Registration::getAllToApprove($current_larp));
		    if ($approval_count>0) echo "$approval_count deltagare som inte är med hela tiden väntar på att deras avgifter ska <a href='registered_persons_parttime.php'>kontrolleras</a>.<br>";
		}
		
		?>
		</div>
		
		<div class="content">
            <a href="not_registered_roles.php">Grupper och karaktärer som inte är anmälda (än) i år</a>
		</div>
		
		<div class="content">
		<h2>Ekonomisk översikt</h2>
		<table>

			<tr><td style='font-weight: normal'>Förväntade intäkter:<br>deltagaravgifter för alla anmälda</td>			
			<td align='right'><?php echo number_format((float)Registration::totalIncomeToBe($current_larp), 2, ',', '');?> SEK</td></tr>
			<tr><td style='font-weight: normal'>Förväntade återbetalningar:<br>deltagaravgifter markerade för återbetalning</td>			
			<td align='right'><?php echo number_format((float)Registration::totalRefundsToBe($current_larp), 2, ',', '');?> SEK</td></tr>
			<tr><td  style='font-weight: normal' colspan='2'><hr></td></tr>
			<?php economy_overview($current_larp); ?>
	    </table>
		</div>
		<br>&nbsp;<br>
		<?php if (AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
		    
		    echo "<a href='https://docs.google.com/document/d/1kcoqIp-dVs_CMS2AyKYlyw8gR9mmQV2KiLpSqfazMBU/edit' target='_blank'>Synpunktsdokumentet</a>";
		}
		?>


	</body>
</html>