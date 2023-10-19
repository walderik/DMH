<?php

include_once 'header.php';

include 'navigation.php';

$param = date_format(new Datetime(),"suv");

?>
    <div class="content">   
        <h1>Arrangörer</h1>

		<?php 
		echo "<strong>Hela kampanjen</strong><br>";
		$campaign = $current_larp->getCampaign();
	     $organizers = User::getAllWithAccessToCampaign($campaign);
	     foreach ($organizers as $organizer) {
	         echo $organizer->Name."<br>";
	     }
	     $organizersLarp = User::getAllWithAccessOnlyToLarp($current_larp);
	     if (!empty($organizersLarp)) echo "<br><strong>Enbart detta lajv</strong><br>";
	     $organizersLarp = User::getAllWithAccessOnlyToLarp($current_larp);
	     foreach ($organizersLarp as $organizer) {
	         echo $organizer->Name;
	         if (AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
    	         echo " <a href='logic/remove_organizer.php?userId=$organizer->Id'>";
    	         echo "<i class='fa-solid fa-trash-can' title='Ta bort som arrangör'></i></a>";
	         }
	         echo "<br>";

	     }
	     if (AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
	       echo "<br><a href='choose_users.php?larpId=$current_larp->Id&operation=organizer'>Lägg till arrangör på $current_larp->Name</a>";
	     }
	     ?>
		

        </div>
</body>

</html>        