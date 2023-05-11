<?php
require 'header.php';
include_once '../includes/error_handling.php';

?>
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title><?php echo $current_larp->Name;?></title>
		<link href="../css/style.css" rel="stylesheet" type="text/css">
		<link href="../css/admin_style.css" rel="stylesheet" type="text/css">
		<link rel="icon" type="image/x-icon" href="../images/<?php echo $current_larp->getCampaign()->Icon; ?>">
		<script src="https://kit.fontawesome.com/30d6e99205.js" crossorigin="anonymous"></script>
	</head>
	<body class="loggedin">

	<nav id="navigation">
        <a href="<?php echo $current_larp->getCampaign()->Homepage ?>" class="logo" target="_blank">
        <img src="../images/<?php echo $current_larp->getCampaign()->Icon; ?>" width="30" height="30" title="<?php echo $current_larp->getCampaign()->Name; ?>-hemsida"/>
        </a>
		<a href="../participant/choose_larp.php" class="logo" title="Välj lajv"><?php echo $current_larp->Name;?></a>
              <ul class="links">
              <li class="dropdown"><a href="#" class="trigger-drop">Administration<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="settings.php">Inställningar</a></li>
                 <li><a href="statistics.php">Statistik</a></li>
                <li><a href="registered_persons.php">Deltagare</a></li>
                <li><a href="persons_to_approve.php">Godkänna</a></li>
                <li><a href="kitchen.php">Köket</a></li>
                <li><a href="officials.php">Funktionärer</a></li>
                <li><a href="housing.php">Boende</a></li>
                <li><a href="reserves.php">Reservlista</a></li>
              </ul>
            </li>
              <li class="dropdown"><a href="#" class="trigger-drop">Intriger<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="groups.php">Grupper</a></li>
                <li><a href="roles.php">Karaktärer</a></li>

                <li><a href="telegram_admin.php">Telegram</a></li>
                <li><a href="letter_admin.php">Brev</a></li>
                <li><a href="prop_admin.php">Rekvisita</a></li>
                <li><a href="npc.php">NPC'er</a></li>
              </ul>
            </li>
              <li class="dropdown"><a href="#" class="trigger-drop">Handel<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="titledeed_admin.php">Lagfarter</a></li>
                 <li><a href="resource_admin.php">Resurser</a></li>
              </ul>
            </li>
        	<li><a href="../site-admin/" style="color: red"><i class="fa-solid fa-lock"></i>OM admin</a></li>  
            <li><a href="../participant/" style="color: #99bbff"><i class="fa-solid fa-unlock"></i>Deltagare</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


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

            if ($current_larp->isFull()) {
                echo "<br>Lajvet är fullt nu.";
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