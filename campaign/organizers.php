<?php

include_once 'header.php';

$campaign = $current_larp->getCampaign();
$isMainOrganizer = $campaign->isMainOrganizer($current_user);
$choosen_year = date("Y");




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['year'])) $choosen_year = $_POST['year'];
    if (isset($_POST['person_id'])) {
        $person_id = $_POST['person_id'];
        if ($person_id == 0) {
            $error_message = "Du kan bara välja bland de föreslagna personerna.";
        } else {
            $operation = $_POST['operation'];
            if ($operation == "add_campaign_organizer" && $isMainOrganizer) {
                AccessControl::grantCampaign($person_id, $campaign->Id);
            } elseif ($operation == "add_larp_organizer") {
                $larpId = $_POST['larpId'];
                $larp = LARP::loadById($larpId);
                $person = Person::loadById($person_id);
                if (!empty($larp) && $larp->CampaignId == $campaign->Id)
                    AccessControl::grantLarp($person, $larp);
            } elseif ($operation == "remove_campaign_organizer" && $isMainOrganizer) {
                AccessControl::revokeCampaign($person_id, $campaign->Id);  
            } elseif ($operation == "remove_larp_organizer") {
                $larpId = $_POST['larpId'];
                $larp = LARP::loadById($larpId);
                if (!empty($larp) && $larp->CampaignId == $campaign->Id)
                    AccessControl::revokeLarp($person_id, $larp->Id);
                    
            }
            
        }
    }
}

include 'navigation.php';

if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}


?>
    <div class="content">   
        <h1>Arrangörer i hela kampanjen</h1>

		<?php 


		$organizers = Person::getAllWithAccessToCampaign($campaign);
	     foreach ($organizers as $organizer) {
	         echo $organizer->Name;
	         echo " ".contactEmailIcon($organizer, BerghemMailer::CAMPAIGN);
	         if ($isMainOrganizer && ($organizer->UserId != $current_user->Id)) {
	             echo "<form method='post' style='display:inline-block'>";
	             echo "<input type='hidden' name='operation' value='remove_campaign_organizer'>\n";
	             echo " <button class='invisible' type ='submit'><i class='fa-solid fa-trash-can' title='Ta bort ur kampanjarrangörsgruppen'></i></button>\n";
	             echo "</form>\n";
	         }
	         echo "<br>";
	     }
	     if ($isMainOrganizer) { ?>
	     	<br>
	         <form method="post"  autocomplete="off" style="display: inline;">
	         	Lägg till kampanjarrangör 
	         	<?php autocomplete_person_id('40%', true); ?>
			 </form>		
	         
	     <?php     
	     }
	     ?>
	     
 	     <h2>Arrangörer på enskilda lajv i kampanjen</h2>	     
 	     <?php 
 	     $years = array_reverse(LARP::getAllYears());

 	     ?>
        <form method="POST">
        
        
        <select name="year" id="year">
        <?php 
        foreach ($years as $year) {
          echo "<option value='$year'";
          if (isset($choosen_year) && $year == $choosen_year) echo " selected ";
          echo ">$year</option>";   
        }
        ?>
        </select>
        
        <input type='submit' value='Visa'>
        
        </form>
 	     <?php 
 	     $larps = LARP::getAllForYear($campaign->Id, $choosen_year);
 	     
 	     if (empty($larps)) {
 	         echo "<br>Kampanjen har inget lajv $choosen_year";
 	         exit;
 	     }
 	     
 	     foreach ($larps as $larp) {
 	         
 	         $organizersLarp = Person::getAllWithAccessOnlyToLarp($larp);
    	     echo "<h3>$larp->Name</h3>";
    
    	     foreach ($organizersLarp as $organizer) {
    	         echo $organizer->Name;
    	         echo " ".contactEmailIcon($organizer, BerghemMailer::CAMPAIGN);
	             echo "<form method='post' style='display:inline-block'>";
	             echo "<input type='hidden' name='larpId' value='$larp->Id'>\n";
	             echo "<input type='hidden' name='operation' value='remove_larp_organizer'>\n";
	             echo " <button class='invisible' type ='submit'><i class='fa-solid fa-trash-can' title='Ta bort ur lajvarrangörsgruppen'></i></button>\n";
	             echo "</form>\n";
    	         echo "<br>";
    
    	     }
    	     
    	     ?>
    	     <br>
	         <form method="post"  autocomplete="off" style="display: inline;">
 	             <input type='hidden' name='larpId' value='<?php echo $larp->Id ?>'>
	             <input type='hidden' name='operation' value='add_larp_organizer'>
	         	Lägg till lajvarrangör 
	         	<?php autocomplete_person_id('40%', true); ?>
			 </form>		
	         
 		<?php } ?>

        </div>
</body>

</html>        