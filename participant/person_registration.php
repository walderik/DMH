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
		<h1>Registrering av person</h1>
		<form action="includes/person_registration_save.php" method="post">


			<p>

			Vi behöver veta en del saker om dig som person som är skilt från de karaktärer du spelar.</p>
			<h2>Personuppgifter</h2>
			<p>
				<div class="question">
					<label for="name">För och efternamn</label>
					<br> <input type="text" id="name" name="name"  size="100" maxlength="250" required>
				</div>
				<div class="question">
					<label for="email">E-post</label><br>
					<input type="email" id="email" name="email"  size="100" maxlength="250" required>
				</div>
				<div class="question">
					<label for="socialsecuritynumber">Personnummer</label><br> 
					<div class="explanation">Nummret ska vara ÅÅÅÅMMDD-NNNN om du saknar personnummer/samordningsnummer får du skriva xxxx på de fyra sista.</div>
					<input type="text" id="socialsecuritynumber"
					name="socialsecuritynumber" pattern="\d{8}-\d{4}|\d{8}-x{4}"  size="15" maxlength="13" required>
				</div>
				<div class="question">
					<label for="phoneNumber">Mobilnummer</label>
					<br> <input type="text" id="phoneNumber" name="phoneNumber"  size="100" maxlength="250">
				</div>
				<div class="question">
					<label for="emergencyContact">Närmaste anhörig</label>
					<br> 
					<div class="explanation">Namn, funktion och mobilnummer till närmast anhöriga. Används enbart i nödfall, exempelvis vid olycka. T ex Greta, Mamma, 08-12345678.    
Det bör vara någon som inte är med på lajvet.</div>
    				<textarea id="emergencyContact" name="emergencyContact" rows="4" cols="100">
    				</textarea>
				</div>
			</p>
			
			
			<h2>Lajvrelaterat</h2>
			
			<div class="question">
				<label for="larperType">Vilken typ av lajvare är du?</label><br>
       			<div class="explanation">Tänk igenom ditt val noga. Det är det här som i första hand kommer 
       			att avgöra hur mycket energi arrangörerna kommer lägga ner på dina intriger.     
       			Är du ny på lajv? Vi rekommenderar då att du inte väljer alternativ Myslajvare. 
       			Erfarenhetsmässigt brukar man som ny lajvare ha mer nytta av mycket intriger än en 
       			erfaren lajvare som oftast har enklare hitta på egna infall under lajvet.   
       			Myslajvare får heller ingen handel och blir troligen varken fattigare eller rikare under lajvet.<br><?php LarperType::helpBox(true); ?></div>
                <?php LarperType::selectionDropdown(false,true); ?>
            </div>
				<div class="question">
					<label for="typeOfLarperComment">Kommentar till typ av lajvare</label>
					<br> <input type="text" id="typeOfLarperComment" name="typeOfLarperComment"  size="100" maxlength="250">
				</div>
			<div class="question">
				<label for="experiences">Hur erfaren lajvare är du?</label><br>
       			<div class="explanation"><?php Experience::helpBox(true); ?></div>
                <?php Experience::selectionDropdown(false,true); ?>
            </div>
			<div class="question">
				<label for="notAcceptableIntrigues">Vilken typ av intriger vill du absolut inte spela på?</label>
				<br> 
				<div class="explanation">Eftersom vi inte vill att någon ska må dåligt är det bra att veta vilka begränsningar du som person har vad det gäller intriger.</div>
				<input type="text" id="notAcceptableIntrigues" name="notAcceptableIntrigues" size="100" maxlength="250" >
			</div>

			<h2>Hälsa</h2>
			<div class="question">
				<label for="typesoffood">Viken typ av mat vill du äta?</label>
				<br> 
				<div class="explanation"><?php TypeOfFood::helpBox(true); ?></div>
				<?php TypeOfFood::selectionDropdown(false,true); ?>
			</div>

			<div class="question">
				<label for="normalallergytypes">Har du av de vanligaste mat-allergierna?</label>
				<br> 
				<div class="explanation"><?php NormalAllergyType::helpBox(true); ?></div>
				<?php NormalAllergyType::selectionDropdown(false,true); ?>
			</div>
			
			<div class="question">
				<label for="food_allergies_other">Har du matallergier eller annan specialkost? </label><br>
				<div class="explanation">Om du har allergier eller specialkost som inte täcks av de två ovanstående frågorna vill vi att du skriver om det här.</div>
				<textarea id="food_allergies_other" name="food_allergies_other" rows="4" cols="100">
				</textarea>
			</div>
			
			<div class="question">
				<label for="otherInformation">Övrig information</label><br>
				<div class="explanation">Är det något annat kring din off-person arrangörerna bör veta? Tex andra allergier eller sjukdomar, eller bra kunskaper tex sjukvårdare.</div>
				<textarea id="otherInformation" name="otherInformation" rows="4" cols="100">
				</textarea>
			
			 
			</div>
			

			<div class="question">
			Härmed samtycker jag till att föreningen Berghems
			Vänner får spara och lagra mina uppgifter - såsom namn/
			e-postadress/telefonnummer/hälsouppgifter/annat. Detta för att kunna
			arrangera lajvet.<br>
			<input type="checkbox" id="PUL" name="PUL" value="Ja" required>
  			<label for="PUL">Jag samtycker</label> 
			</div>

			  <input type="submit" value="Registrera">
		</form>
	</div>

</body>
</html>