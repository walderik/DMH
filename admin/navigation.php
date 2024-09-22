<!DOCTYPE html>
<html>
<head>
    <link href="../css/navigation_admin.css" rel="stylesheet" type="text/css">
	
	<?php include '../common/navigation_beginning.php';?> 
	  <a href="index.php"><i class="fa-solid fa-house"></i> Hem</a>
	  
	  <?php if (AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) { ?>
	  

	  <div class="dropdown">
	    <button class="dropbtn">Inställningar 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="status.php">Lajvstatus</a>
	    	
	      	<a href="larp_form.php?operation=update&id=<?php echo $current_larp->Id?>">Basinställningar</a>
	      	<a href="payment_information_admin.php">Avgifter, inklusive mat</a>
	    </div>
	  </div> 
	  <?php } ?> 


	  <div class="dropdown">
	    <button class="dropbtn">Praktiskt 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="../common/mail_admin.php">Skickad epost</a>
            <a href="registered_persons.php">Deltagare</a>
            <a href="approval.php">Godkänna</a>
            <a href="kitchen.php">Köket</a>
            <a href="officials.php">Funktionärer</a>
            <a href="housing.php">Boende</a>
            <a href="reserves.php">Reservlista</a>
            <a href="economy.php">Kassabok</a>
            <a href="invoice_admin.php">Fakturor</a>
            <a href="refund_persons.php">Återbetalning</a>
            <a href="reports.php">Rapporter</a>
            <a href="statistics.php">Statistik</a>
            <a href="evaluation_result.php">Utvärdering</a>
		    <a href="ssn_check.php">Medlemskontroll</a> 


	    </div>
	  </div> 
	  <div class="dropdown">
	    <button class="dropbtn">Spel 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
            <a href="roles.php">Karaktärer</a>
            <a href="groups.php">Grupper</a>
            <a href="subdivision_admin.php">Grupperingar</a>
            <a href="search_role_selectiondata.php">Sökning på karaktärer</a>
            <a href="intrigue_admin.php">Intrigspår</a>
            <a href="timeline_admin.php">Körschema</a>
            <a href="commerce.php">Handel</a>
            
			<?php if ($current_larp->hasAlchemy()) {?>
            <a href="alchemy.php">Alkemi</a>
            <?php }?>
            
			<?php if ($current_larp->hasMagic()) {?>
            <a href="magic.php">Magi</a>
            <?php }?>
            
			<?php if ($current_larp->hasVisions()) {?>
            <a href="vision_admin.php">Syner</a>
            <?php }?>
            			
			<?php if ($current_larp->hasTelegrams()) {?>
            <a href="telegram_admin.php">Telegram</a>
            <?php }?>
            
			<?php if ($current_larp->hasLetters()) {?>
            <a href="letter_admin.php">Brev</a>
            <?php }?>
            
			<?php if ($current_larp->hasRumours()) {?>
            <a href="rumour_admin.php">Rykten</a>
            <?php }?>
            
            <a href="prop_admin.php">Rekvisita</a>
            <a href="npc.php">NPC'er</a>
            <a href="what_happened.php">Vad hände?</a>
	    </div>
	  </div> 
	  
	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?>   

