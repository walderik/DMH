<?php

include_once 'header.php';

include 'navigation.php';

$param = date_format(new Datetime(),"suv");

?>
    <div class="content">   
        <h1>Arrangörer</h1>

		<?php 
		$campaign = $current_larp->getCampaign();
		$isMainOrganizer = $campaign->isMainOrganizer($current_user);
		
		echo "<strong>Hela kampanjen</strong><br>";

		$organizers = Person::getAllWithAccessToCampaign($campaign);
	     foreach ($organizers as $organizer) {
	         echo $organizer->Name;
	         echo " ".contactEmailIcon($organizer, false);
	         if ($isMainOrganizer) {
    	         echo " <a href='logic/remove_campaign_organizer.php?personId=$organizer->Id'>";
    	         echo "<i class='fa-solid fa-trash-can' title='Ta bort som arrangör'></i></a>";
	         }
	         echo "<br>";
	     }
	     if ($isMainOrganizer) {
	         echo "<br><a href='choose_persons.php?campaignId=$campaign->Id&operation=campaign_organizer'>Lägg till kampanjarrgangör på $campaign->Name</a>";
	         
	     }
	     
	     $organizersLarp = Person::getAllWithAccessOnlyToLarp($current_larp);
	     if (!empty($organizersLarp)) echo "<br><strong>Enbart detta lajv</strong><br>";

	     foreach ($organizersLarp as $organizer) {
	         echo $organizer->Name;
	         echo " ".contactEmailIcon($organizer, false);
	         echo " <a href='logic/remove_organizer.php?personId=$organizer->Id'>";
	         echo "<i class='fa-solid fa-trash-can' title='Ta bort som arrangör'></i></a>";
	         echo "<br>";

	     }

	       echo "<br><a href='choose_persons.php?larpId=$current_larp->Id&operation=organizer'>Lägg till arrangör på $current_larp->Name</a>";

	     ?>
		

        </div>
</body>

</html>        