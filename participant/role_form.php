<?php

require 'header.php';

$current_persons = $current_user->getPersons();

if (empty($current_persons)) {
    header('Location: index.php&error=no_person');
    exit;
}

?>

    <nav id="navigation">
      <a href="#" class="logo"><?php echo $current_larp->Name; ?></a>
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
		<form action="includes/role_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">


			<p>Vi vill veta vilken karaktär du vill spela.<br />
			Om du vill spela en av lajvets sökta roller ber vi dig att kontakta arrangörerna innan du fyller i din anmälan.<br />
			Tänk på att din karaktär också måste godkännas av arrangörerna.    
			</p>
			<div class="question">
				<label for="PersonId">Deltagare</label><br>
				<div class="explanation">Vilken deltagare vill du registrera en karaktär för?</div>
				<?php selectionDropdownByArray('PersonId', $current_persons, false, true) ?>
			</div>

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
Är gruppen anmäld, men ändå inte syns här så måste du kontakta arrangörerna som får se till att den är valbar i listan.<br>Anmäl dig bara till en grupp om du har fått ok på det från gruppledaren. Om du vill skapa en egen grupp gör du det i det <a href="group_form-php">här formuläret</a>.</div>
                <?php LARP_Group::selectionDropdown($current_larp); ?>
            </div>
				
			<div class="question">
				<label for="ReasonForBeingInSlowRiver">Varför befinner sig karaktären i Slow River?</label><br> 
				<div class="explanation">Självklart har din karaktär en anledning att vara i just den här hålan. Om din karaktär bor här så finns det en anledning att bo kvar.    
Är du besökande så lär det finnas en bra anledning att inte bara åka vidare efter en natts vila, utan stanna till ett par nätter.    
Kommer du tillbaka år efter år så är det säkert en riktigt bra anledning.</div>
				<textarea id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100"><?php echo $role->ReasonForBeingInSlowRiver; ?></textarea>
			</div>
				
			<div class="question">
				<label for="PreviousLarps">Tidigare lajv</label><br> 
				<div class="explanation">Död mans hand är ett kampanjlajv. Det innebär att allt din karaktär gör ett år och andra gör mot den ska påverka det här lajvet.    
					Det är inte så farligt som det låter, utan är ett bra sätt att ge större djup i lajvet och göra din egen karaktär intressantare både för dig själv och andra.<br><br>
					Om du var med förra året med din karaktär, vad hände med din karaktär som är bra att komma ihåg? Gjorde den några särskilt bra affärer? Var den med i en duell? Blev den svindlad eller svindlade den någon? Hur gick det med kärleken?<br><br>
					Har din karaktär gjort något minnesvärt tidigare år?
				</div>
				<textarea id="PreviousLarps" name="PreviousLarps" rows="8" cols="100"><?php echo $role->PreviousLarps; ?></textarea>
			</div>
			<div class="question">
				<label for="Religion">Religion</label><br>
				<div class="explanation">Vissa religioner har bättre anseende än andra. Är du kristen, så ange inte bara det utan vilken typ av kristen du är. Katoliker har generellt sett fortfarande lite sämre anseende än andra kristna.</div>
				<input type="text" id="Religion" name="Religion" value="<?php echo $role->Religion; ?>"  size="100" maxlength="250" required>
			</div>
			<div class="question">
				<label for="WealthsId">Hur rik är karaktären?</label><br>
       			<div class="explanation">Om du anser att du har rikedom 3 eller högre förväntas du i regel ha någon form av affärer på gång. Det kan vara att sälja saker din gård producerat, leta guld eller nästan vad som helst som gör att man inte är fattig längre.   Det kommer att vara ett begränsat antal stenrika på lajvet och vi godkänner i regel inte nya. Undantag kan naturligtvis förekomma om det gynnar lajvet.   Däremot är Död Mans Hand ett kampanjlajv så det går att spela sig till att bli stenrik. Det går också att bli fattig om man är stenrik.<?php Wealth::helpBox(true); ?></div>
                <?php Wealth::selectionDropdown(false,true); ?>
            </div>

			<div class="question">
				<label for="DarkSecret">Mörk hemlighet</label><br> 
				<div class="explanation">Alla har någonting de inte vill berätta så gärna för andra. Vad har din karaktär för mörk hemlighet?    
Du måste ange en mörk hemlighet.    
Det kan kännas svårt att göra karaktären sårbar på det här sättet, men försök. Det ger mer spännande spel.</div>
				<textarea id="DarkSecret" name="DarkSecret" rows="4" cols="100" required><?php echo $role->DarkSecret; ?> </textarea>
			</div>
			<div class="question">
				<label for="DarkSecretIntrigueIdeas">Mörk hemlighet - intrig ideer</label><br>
				<div class="explanation">Hur kan vi spela på din mörka hemlighet?</div>
				<input type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo $role->DarkSecretIntrigueIdeas; ?>"  size="100" maxlength="250" required>
			</div>
			<div class="question">
				<label for="IntrigueSuggestions">Intrigideer</label><br> 
				<div class="explanation">Är det någon typ av spel du särskilt önskar eller något som du inte önskar spel på?  Exempel kan vara "Min karaktär har: en skuld till en icke namngiven karaktär/mördat någon/svikit sin familj/ett oäkta barn/lurat flera personer på pengar". </div>
				<textarea id="IntrigueSuggestions" name="IntrigueSuggestions" rows="4" cols="100"><?php echo $role->IntrigueSuggestions; ?></textarea>
			</div>
			<div class="question">
				<label for="NotAcceptableIntrigues">Saker karaktären inte vill spela på</label><br>
				<div class="explantion">Är det något den här karaktären aldrig skulle göra? Vill du helst undvika farligt spel är det också bra att ange.</div>
				<input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo $role->NotAcceptableIntrigues; ?>"  size="100" maxlength="250">
			</div>




			<div class="question">
				<label for="OtherInformation">Övrig information</label><br>
				<div class="explanation">Är det något annat kring karaktären arrangörerna bör veta?</div>
				<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100"><?php echo $role->OtherInformation; ?></textarea>
			
			 
			</div>


		
			<input type="submit" value="Registrera">
		</form>
	</div>

</body>
</html>