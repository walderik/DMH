<?php

require 'header.php';

// TODO Lägg till diverse kontroller som behövs för att kolla om man bland annat har en person registrerad.

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
	       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


	<div class="content">
		<h1>Registrering av grupp</h1>
		<form action="includes/group_registration_save.php" method="post">


			<p>En grupp är en gruppering av roller som gör något tillsammans på
				lajvet. Exempelvis en familj på lajvet, en rånarliga eller ett rallarlag.</p>
			<h2>Gruppledare</h2>
			<p>(TODO Ersätt det här med att välja en av sina registrerade personer, eller bara visa den man har om man bara har en person registrerad.)<br />
			
				Gruppledaren är den som arrangörerna kommer att kontakta när det
				uppstår frågor kring gruppen.
				<div class="question">
				<label for="group_leader_name">Gruppledarens
					namn</label><br> <input type="text" id="group_leader_name"
					name="group_leader_name" required>
					</div>
					<div class="question">
					<label for="email">E-post</label><br>
				<input type="email" id="email" name="email" required>
				</div>
			</p>
			
			
			<h2>Information om gruppen</h2>
			
			
			<div class="question">
				<label for="group_name">Gruppens namn</label><br> <input
					type="text" id="group_name" name="group_name" required>
			</div>
			
			<div class="question">
			<label for="group_description">Beskrivning av gruppen</label><br>
			<textarea id="group_description" name="group_description" rows="4" cols="50">
			</textarea>
			
			 
			</div>
			
			
			<div class="question">
				<label for="approximate_number_of_participants">Ungefär hur många
					gruppmedlemmar kommer ni att bli?</label><br> <input type="text"
					id="approximate_number_of_participants"
					name="approximate_number_of_participants" required>
			</div>
						<div class="question">
			<label for="housing_request">Hur vill ni bo som grupp?</label><br>
       		<div class="explanation"><?php HousingRequest::helpBox(true); ?></div>
            <?php

            HousingRequest::selectionDropdown(false,true);
            
            ?>

        </div>
        			<div class="question">
				<label for="need_fireplace">Behöver ni eldplats?</label><br> <input
					type="radio" id="need_fireplace_yes" name="need_fireplace" value="yes"> <label
					for="need_fireplace_yes">Ja</label><br> <input type="radio" id="need_fireplace_no"
					name="need_fireplace" value="no"> <label for="need_fireplace_no">Nej</label>
			</div>
			<div class="question">
				<label for="friends">Vänner</label><br>
				<textarea id="friends" name="friends" rows="4" cols="50">
				</textarea>
			</div>
			<div class="question">
				<label for="enemies">Fiender</label><br>
				<textarea id="enemies" name="enemies" rows="4" cols="50">
				</textarea>
			</div>


			<div class="question">
			<label for="wealth">Hur rik anser du att ni är?</label>
			<div class="explanation"><?php Wealth::helpBox(true); ?></div>

			
            <?php

            Wealth::selectionDropdown();
            
            ?> 
			
			
			</div>
			<div class="question">
			<label for="placeofresidence">Var bor gruppen?</label>
			<div class="explanation"><?php PlaceOfResidence::helpBox(true); ?></div>
			
			
            <?php
            PlaceOfResidence::selectionDropdown();
            ?> 

			</div>
			<div class="question">
 
			
			
			<label for="want_intrigue">Vill ni ha en
			arrangörsskriven gruppintrig inför lajvet? </label>
			<div class="explanation">Om ni svarar "Nej" är det
			inte en garanti för att ni inte får några intriger ändå. Speciellt om
			ni redan är en existerande grupp i kampanjen så är det troligare att
			ni är inblandade i något.</div>
			<input
					type="radio" id="want_intrigue_yes" name="want_intrigue" value="yes"> <label
					for="want_intrigue_yes">Ja</label><br> <input type="radio" id="want_intrigue_no"
					name="want_intrigue" value="no"> <label for="want_intrigue_no">Nej</label>
			</div>
			
			<div class="question">
			Vilka typer av gruppintriger är ni intresserade av?
			<div class="explanation"><?php IntrigueType::helpBox(true); ?></div>
			
			
			<?php
			IntrigueType::selectionDropdown(true);
            ?>
			</div>
			<div class="question">
			<label for="intrigue_ideas">Intrigidéer</label>
			<div class="explanation">
			Har ni några grupprykten som ni vill ha hjälp med att sprida? 
			</div>
			<textarea id="intrigue_ideas" name="intrigue_ideas" rows="4" cols="50"></textarea>
			
			
			</div>
						
			<div class="question">
			<label for="other_information">Något annat arrangörerna bör veta om er grupp?</label><br>
			<textarea id="other_information" name="other_information" rows="4" cols="50">
			</textarea>
			
			 
			</div>
			
			
			<div class="question">
			Genom att kryssa i denna ruta så lovar jag med
			heder och samvete att jag har läst igenom alla hemsidans regler och
			förmedlat dessa till mina gruppmedlemmar. Vi har även alla godkänt
			dem och är införstådda med vad som förväntas av oss som grupp av
			deltagare på lajvet. Om jag inte har läst reglerna så kryssar jag
			inte i denna ruta.<br>
			
			<input type="checkbox" id="rules" name="rules" value="Ja" required>
  			<label for="rules">Jag lovar</label> 
			</div>
			<div class="question">
			Härmed samtycker jag till att föreningen Berghems
			Vänner får spara och lagra mina uppgifter - såsom namn/
			e-postadress/telefonnummer/hälsouppgifter/annat. Detta för att kunna
			arrangera lajvet.<br>
			<input type="checkbox" id="PUL" name="PUL" value="Ja" required>
  			<label for="PUL">Jag samtycker</label> 
			</div>

			  <input type="submit" value="Skicka">
		</form>
	</div>

</body>
</html>