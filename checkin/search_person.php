<?php
include_once 'header.php';



include 'navigation.php';
?>
		<div class="header">
			<i class="fa-solid fa-user"></i>
			Sök person att checka in
		</div>
   		<div class='itemcontainer'>


	<form autocomplete="off" action="checkin_person.php" method="post">
		<?php autocomplete_person_id('60%', true, $current_larp->Id); ?>
		</form>
		
				</div>