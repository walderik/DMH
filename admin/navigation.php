<!DOCTYPE html>
<html>
<head>
    <link href="../css/navigation_admin.css" rel="stylesheet" type="text/css">
	
	<?php include '../common/navigation_beginning.php';?> 
	  <?php if (AccessControl::hasAccessCampaign($current_person, $current_larp->CampaignId) || AccessControl::hasAccessOther($current_person, AccessControl::ADMIN)) { ?>

	  <div class="dropdown">
	    <button class="dropbtn">Inställningar 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
	      	<a href="../admin/status.php">Lajvstatus</a>
	    	
	      	<a href="../admin/larp_form.php?operation=update&id=<?php echo $current_larp->Id?>">Basinställningar</a>
	      	<a href="../admin/payment_information_admin.php">Avgifter, inklusive mat</a>
	    </div>
	  </div> 
	  <?php } ?> 


	  <div class="dropdown">
	    <button class="dropbtn">Praktiskt 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
            <a href="../admin/registered_persons.php">Deltagare</a>
            <a href="../admin/approval.php">Godkänna</a>
            <a href="../admin/kitchen.php">Köket</a>
            <a href="../admin/officials.php">Funktionärer</a>
            <a href="../admin/housing.php">Boende</a>
            <a href="../admin/reserves.php">Reservlista</a>
            <a href="../admin/economy.php">Kassabok</a>
            <a href="../admin/invoice_admin.php">Fakturor</a>
            <a href="../admin/refund_persons.php">Återbetalning</a>
            <a href="../admin/reports.php">Rapporter</a>
            <a href="../admin/statistics.php">Statistik</a>
            <a href="../admin/evaluation_result.php">Utvärdering</a>



	    </div>
	  </div> 
	  <div class="dropdown">
	    <button class="dropbtn">Spel 
	      <i class="fa fa-caret-down"></i>
	    </button>
	    <div class="dropdown-content">
            <a href="../admin/roles.php">Karaktärer</a>
            <a href="../admin/groups.php">Grupper</a>
            <a href="../admin/subdivision_admin.php">Grupperingar</a>
            <a href="../admin/search_role_selectiondata.php">Sökning på karaktärer</a>
            <a href="../admin/intrigue_admin.php">Intrigspår</a>
            <a href="../admin/intrigue_creator_page.php">Intrigskapare</a>
            <a href="../admin/timeline_admin.php">Körschema</a>
            <a href="../admin/commerce.php">Handel</a>
            
			<?php if ($current_larp->hasAlchemy()) {?>
            <a href="../admin/alchemy.php">Alkemi</a>
            <?php }?>
            
			<?php if ($current_larp->hasMagic()) {?>
            <a href="../admin/magic.php">Magi</a>
            <?php }?>
            
			<?php if ($current_larp->hasVisions()) {?>
            <a href="../admin/vision_admin.php">Syner</a>
            <?php }?>
            			
			<?php if ($current_larp->hasTelegrams()) {?>
            <a href="../admin/telegram_admin.php">Telegram</a>
            <?php }?>
            
			<?php if ($current_larp->hasLetters()) {?>
            <a href="../admin/letter_admin.php">Brev</a>
            <?php }?>
            
			<?php if ($current_larp->hasRumours()) {?>
            <a href="../admin/rumour_admin.php">Rykten</a>
            <?php }?>
            
            <a href="../admin/prop_admin.php">Rekvisita</a>
            <a href="../admin/npc.php">NPC'er</a>
            <a href="../admin/what_happened.php">Vad hände?</a>
	    </div>
	  </div> 
	  
	<?php include '../common/navigation_site_part_selector.php';?>  
	<?php include '../common/navigation_end.php';?>   

