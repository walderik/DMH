<?php

require 'header.php';


include 'navigation.php';
?>
	<div class='itemselector'>
		<div class="header">

			<i class="fa-solid fa-user"></i>
			<?php echo $current_person->Name;?>&nbsp;<a href='person_form.php?operation=update'><i class='fa-solid fa-pen'></i></a>
		</div>
   		<div class='itemcontainer'>
       	<div class='itemname'>Personnummer</div>
		<?php echo $current_person->SocialSecurityNumber;?>
		</div>
		

   		<div class='itemcontainer'>
       	<div class='itemname'>E-post</div>
		<?php echo $current_person->Email;?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Mobilnummer</div>
		<?php echo $current_person->PhoneNumber;?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Närmaste anhörig</div>
		<?php echo nl2br(htmlspecialchars($current_person->EmergencyContact));?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Lajverfarenhet</div>
		<?php echo $current_person->getExperience()->Name;?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Vilken typ av intriger vill du absolut inte spela på?</div>
		<?php echo nl2br(htmlspecialchars($current_person->NotAcceptableIntrigues));?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Allergi</div>
		<?php echo commaStringFromArrayObject($current_person->getNormalAllergyTypes());?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Annat kring matallergier eller annan specialkost?</div>
		<?php echo nl2br(htmlspecialchars($current_person->FoodAllergiesOther));?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Generella boendehänsyn</div>
		<?php echo nl2br(htmlspecialchars($current_person->HousingComment));?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Fysisk och mental hälsa</div>
		<?php echo nl2br(htmlspecialchars($current_person->HealthComment));?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Övrig information</div>
		<?php echo nl2br(htmlspecialchars($current_person->OtherInformation));?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Tillstånd att visa namn</div>
		<?php echo ja_nej($current_person->hasPermissionShowName());?>
		</div>
		
   		<div class='itemcontainer'>
       	<div class='itemname'>Tar emot epost</div>
		<?php echo ja_nej($current_person->isSubscribed());?>
		</div>

	</div>


</body>
</html>
