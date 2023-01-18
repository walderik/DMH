<?php

require 'header.php';


?>

    <nav id="navigation">
      <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
      <ul class="links">
        <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
       	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
      </ul>
    </nav>

    <?php
    $role = Role::newWithDefault();
    
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
        $operation = "new";
        if (isset($_GET['operation'])) {
            $operation = $_GET['operation'];
        }
        if ($operation == 'new') {
        } elseif ($operation == 'update') {
            $role = Role::loadById($_GET['id']);
        } else {
        }
    }
      
    function default_value($field) {
        GLOBAL $role;
        $output = "";

        switch ($field) {
            case "operation":
                if (is_null($role->Id)) {
                    $output = "insert";
                    break;
                }
                $output = "update";
                break;            
            case "action":
                if (is_null($role->Id)) {
                    $output = "Lägg till";
                    break;
                }
                $output = "Uppdatera";
                break;
        }

        echo $output;
    }
    
?>

	<div class="content">
		<h1>Registrering av karaktär</h1>
		<form action="includes/person_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">


			<p>Vi vill veta vilken karaktär du vill spela. Om du vill spela en av lajvets sökta roller ber vi dig att kontakta arrangörerna innan du fyller i din anmälan.    
Tänk på att din karaktär också måste godkännas av arrangörerna.    
</p>
			<h2>Personuppgifter</h2>
			<p>
				<div class="question">
					<label for="Name">Karaktärens namn</label>
					<br> <input type="text" id="Name" name="Name" value="<?php echo $role->Name; ?>" size="100" maxlength="250" required>
				</div>
				<div class="question">
					<label for="Profession">Yrke</label><br>
					<div class="explanation">Vad jobbar din karaktär med för att överleva?   Vill du ha ett yrke som kan innebära en central roll i lajvet, så vill vi helst att du först kontaktar arrangörerna innan du anmäler den.    Det gäller poster som borgmästare, bypräst eller sheriff.   Har din karaktär tidigare haft en viktigare post har du naturligtvis oftast förtur till att få fortsätta spela att din karaktär har det yrket. Vi vill helst inte att du spelar prostituerad.</div>
					<input type="text" id="Profession" name="Profession" value="<?php echo $role->Profession; ?>"  size="100" maxlength="250" required>
				</div>

				<div class="question">
					<label for="Birthplace">Var är karaktären född?</label><br>
					<input type="text" id="Birthplace" name="Birthplace" value="<?php echo $role->Birthplace; ?>"  size="100" maxlength="250" required>
				</div>
				<div class="question">
					<label for="CharactersWithRelations">Relationer med andra</label><br> 
					<div class="explanation">Tre karaktärer (på lajvet eller som bakgrundskaraktärer) som är viktiga för din karaktär och mycket kort hur vi kan ge spel på dessa karaktärer.</div>
					<textarea id="CharactersWithRelations" name="CharactersWithRelations" rows="4" cols="100"><?php echo $role->CharactersWithRelations; ?></textarea>
				</div>
				
				
				
				
				
				<div class="question">
					<label for="Description">Beskrivning</label><br> 
					<div class="explanation">Beskriv allt om din karaktär som arrangörerna behöver veta.</div>
					<textarea id="Description" name="Description" rows="4" cols="100"><?php echo $role->Description; ?></textarea>
				</div>
				
				
				
			<div class="question">
				<label for="GroupsId">Vilken grupp är karaktären med i?</label><br>
       			<div class="explanation">Finns inte din grupp med på anmälan ska du kontakta din gruppledare och se till att den är anmäld innan du själv anmäler dig.    
Är gruppen anmäld, men ändå inte syns här så måste du kontakta arrangörerna som får se till att den är valbar i listan.<br>Anmäl dig bara till en grupp om du har fått ok på det från gruppledaren. Om du vill skapa en egen grupp gör du det i det <a href="group_form-php">här formuläaret</a>.</div>
                <?php Group::selectionDropdown(); ?>
            </div>
				
				
				<div class="question">
					<label for="PreviousLarps">Tidigare lajv</label><br> 
					<div class="explanation">Vad har hänt ??</div>
					<textarea id="PreviousLarps" name="PreviousLarps" rows="4" cols="100"><?php echo $role->PreviousLarps; ?></textarea>
				</div>
				<div class="question">
					<label for="ReasonForBeingInSlowRiver">ReasonForBeingInSlowRiver</label><br> 
					<div class="explanation">Vad har hänt ??</div>
					<textarea id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100"><?php echo $role->ReasonForBeingInSlowRiver; ?></textarea>
				</div>
				<div class="question">
					<label for="Religion">Religion</label><br>
					<input type="text" id="Religion" name="Religion" value="<?php echo $role->Religion; ?>"  size="100" maxlength="250" required>
				</div>
				<div class="question">
					<label for="DarkSecret">DarkSecret</label><br> 
					<div class="explanation">Beskriv karaktären.</div>
					<textarea id="DarkSecret" name="DarkSecret" rows="4" cols="100"><?php echo $role->DarkSecret; ?></textarea>
				</div>
				<div class="question">
					<label for="DarkSecretIntrigueIdeas">DarkSecretIntrigueIdeas</label><br>
					<input type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo $role->DarkSecretIntrigueIdeas; ?>"  size="100" maxlength="250" required>
				</div>
				<div class="question">
					<label for="IntrigueSuggestions">IntrigueSuggestions</label><br> 
					<div class="explanation">Beskriv karaktären.</div>
					<textarea id="IntrigueSuggestions" name="IntrigueSuggestions" rows="4" cols="100"><?php echo $role->IntrigueSuggestions; ?></textarea>
				</div>
				<div class="question">
					<label for="NotAcceptableIntrigues">NotAcceptableIntrigues</label><br>
					<input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo $role->NotAcceptableIntrigues; ?>"  size="100" maxlength="250" required>
				</div>



			<div class="question">
				<label for="WealthsId">Hur rik är karaktären?</label><br>
       			<div class="explanation"><?php Wealth::helpBox(true); ?></div>
                <?php Wealth::selectionDropdown(false,true); ?>
            </div>


			<div class="question">
				<label for="OtherInformation">Övrig information</label><br>
				<div class="explanation">Är det något annat kring karaktären arrangörerna bör veta?</div>
				<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100"><?php echo $role->OtherInformation; ?></textarea>
			
			 
			</div>

			</p>
			
			
			<h2>Lajvrelaterat</h2>
			
			<div class="question">
				<label for="LarperTypesId">Vilken typ av lajvare är du?</label><br>
       			<div class="explanation">Tänk igenom ditt val noga. Det är det här som i första hand kommer 
       			att avgöra hur mycket energi arrangörerna kommer lägga ner på dina intriger.     
       			Är du ny på lajv? Vi rekommenderar då att du inte väljer alternativ Myslajvare. 
       			Erfarenhetsmässigt brukar man som ny lajvare ha mer nytta av mycket intriger än en 
       			erfaren lajvare som oftast har enklare hitta på egna infall under lajvet.   
       			Myslajvare får heller ingen handel och blir troligen varken fattigare eller rikare under lajvet.<br><?php LarperType::helpBox(true); ?></div>
                <?php LarperType::selectionDropdown(false,true); ?>
            </div>
				<div class="question">
					<label for="TypeOfLarperComment">Kommentar till typ av lajvare</label>
					<br> <input type="text" id="TypeOfLarperComment" value="<?php echo $role->TypeOfLarperComment; ?>" name="TypeOfLarperComment"  size="100" maxlength="250">
				</div>
			<div class="question">
				<label for="ExperiencesId">Hur erfaren lajvare är du?</label><br>
       			<div class="explanation"><?php Experience::helpBox(true); ?></div>
                <?php Experience::selectionDropdown(false,true); ?>
            </div>
			<div class="question">
				<label for="NotAcceptableIntrigues">Vilken typ av intriger vill du absolut inte spela på?</label>
				<br> 
				<div class="explanation">Eftersom vi inte vill att någon ska må dåligt är det bra att veta vilka begränsningar du som person har vad det gäller intriger.</div>
				<input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo $role->NotAcceptableIntrigues; ?>" size="100" maxlength="250" >
			</div>

			<h2>Hälsa</h2>
			<div class="question">
				<label for="TypesOfFoodId">Viken typ av mat vill du äta?</label>
				<br> 
				<div class="explanation"><?php TypeOfFood::helpBox(true); ?></div>
				<?php TypeOfFood::selectionDropdown(false,true); ?>
			</div>

			<div class="question">
				<label for="NormalAllergyType">Har du av de vanligaste mat-allergierna?</label>
				<br> 
				<div class="explanation"><?php NormalAllergyType::helpBox(true); ?></div>
				<?php NormalAllergyType::selectionDropdown(false,true); ?>
			</div>
			
			<div class="question">
				<label for="FoodAllergiesOther">Har du matallergier eller annan specialkost? </label><br>
				<div class="explanation">Om du har allergier eller specialkost som inte täcks av de två ovanstående frågorna vill vi att du skriver om det här.</div>
				<textarea id="FoodAllergiesOther" name="FoodAllergiesOther" rows="4" cols="100"><?php echo $role->FoodAllergiesOther; ?></textarea>
			</div>
			
			<div class="question">
				<label for="OtherInformation">Övrig information</label><br>
				<div class="explanation">Är det något annat kring din off-person arrangörerna bör veta? Tex andra allergier eller sjukdomar, eller bra kunskaper tex sjukvårdare.</div>
				<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100"><?php echo $role->OtherInformation; ?></textarea>
			
			 
			</div>			

			<div class="question">
			Härmed samtycker jag till att föreningen Berghems
			Vänner får spara och lagra mina uppgifter - såsom namn/
			e-postadress/telefonnummer/hälsouppgifter/annat. Detta för att kunna
			arrangera lajvet.<br>
			<input type="checkbox" id="Rules" name="Rules" value="Ja" required>
  			<label for="Rules">Jag samtycker</label> 
			</div>

			  <input type="submit" value="Registrera">
		</form>
	</div>

</body>
</html>