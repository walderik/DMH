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
		$organizers = Person::getAllWithAccessToCampaign($campaign);
	     foreach ($organizers as $organizer) {
	         echo $organizer->Name."<br>";
	     }
	     $organizersLarp = Person::getAllWithAccessOnlyToLarp($current_larp);
	     if (!empty($organizersLarp)) echo "<br><strong>Enbart detta lajv</strong><br>";

	     foreach ($organizersLarp as $organizer) {
	         echo $organizer->Name;
	         echo " <a href='logic/remove_organizer.php?personId=$organizer->Id'>";
	         echo "<i class='fa-solid fa-trash-can' title='Ta bort som arrangör'></i></a>";
	         echo "<br>";

	     }

	       echo "<br><a href='choose_persons.php?larpId=$current_larp->Id&operation=organizer'>Lägg till arrangör på $current_larp->Name</a>";

	     ?>
		

        </div>
</body>

</html>        