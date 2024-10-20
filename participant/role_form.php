<?php

require 'header.php';

$admin = false;
if (isset($_GET['admin'])) $admin = true;


$current_persons = $current_user->getPersons();
if (empty($current_persons) && !$admin) {
    header('Location: index.php?error=no_person');
    exit;
}

$role = Role::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "new";
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    else {

    }
    if ($operation == 'new') {
    } elseif ($operation == 'update') {
        $role = Role::loadById($_GET['id']);
    } else {
    }
}

if ($role->isRegistered($current_larp) && !$role->userMayEdit($current_larp)) {
    header('Location: ' . $role->getViewLink());
    exit;
}

if ($operation == 'update' && Person::loadById($role->PersonId)->UserId != $current_user->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
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
                $output = "Registrera";
                break;
            }
            $output = "Uppdatera";
            break;
    }
    
    echo $output;
}

$campaign = $current_larp->getCampaign();

include 'navigation.php';

?>

	<script>
	
	function setFieldState(isYes) {
		var intrigueDivs = document.getElementsByClassName("intrigue");
		var requiredFields = document.getElementsByClassName("requiredIntrigueField");
		var larpertype = document.getElementsByName("LarperTypeId");
		var wealths = document.getElementsByName("WealthId");
		var religions = document.getElementsByName("ReligionId");
		var placeofresidences = document.getElementsByName("PlaceOfResidenceId");
		var believes = document.getElementsByName("BeliefId");
        if (isYes) {
    		for (var i = 0; i < intrigueDivs.length; i++) {
        		intrigueDivs[i].style.display = "none";
    		}
    		for (var i = 0; i < requiredFields.length; i++) {
        		requiredFields[i].required = false;        		
    		}
    		for (var i = 0; i < larpertype.length; i++) {
        		larpertype[i].required = false;  
    		}
    		for (var i = 0; i < wealths.length; i++) {
        		wealths[i].required = false;  
    		}
    		for (var i = 0; i < religions.length; i++) {
    			religions[i].required = false;  
    		}
    		for (var i = 0; i < placeofresidences.length; i++) {
        		placeofresidences[i].required = false;        		
    		}
    		for (var i = 0; i < believes.length; i++) {
    			believes[i].required = false;        		
    		}
        } else {
    		for (var i = 0; i < intrigueDivs.length; i++) {
        		intrigueDivs[i].style.display = "block";
    		}
     		for (var i = 0; i < requiredFields.length; i++) {
        		requiredFields[i].required = true;
    		}
    		for (var i = 0; i < larpertype.length; i++) {
        		larpertype[i].required = true;  
    		}
     		for (var i = 0; i < wealths.length; i++) {
        		wealths[i].required = true;        		
    		}
     		for (var i = 0; i < religions.length; i++) {
     			religions[i].required = true;        		
    		}
    		for (var i = 0; i < placeofresidences.length; i++) {
        		placeofresidences[i].required = true;  
    		}
       		for (var i = 0; i < believes.length; i++) {
    			believes[i].required = true;  
    		}
        }
    }
    
    function handleRadioClick() {
        if (document.getElementById("myslajvare_yes").checked) {
            setFieldState(true);
        } else if (document.getElementById("myslajvare_no").checked) {
            setFieldState(false);
        }
    }

	function defaultMyslajvare() {
		document.getElementById("myslajvare_yes").checked = true;
		setFieldState(true);
	}

	function defaultEjMyslajvare() {
		document.getElementById("myslajvare_no").checked = true;
		setFieldState(false);
	}
	
	
	</script>

	
	<div class="content">
		<h1><?php default_value('action'); ?> karaktär</h1>
		<form action="logic/role_form_save.php" method="post">
    		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
    		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">


			<p>Vi vill veta vilken karaktär du vill spela.<br />
			Om du vill spela en av lajvets sökta karaktärer ber vi dig att kontakta arrangörerna innan du fyller i din anmälan.<br />
			Tänk på att din karaktär också måste godkännas av arrangörerna.   <br>
			<br>
			Efter anmälan kommer du att kunna ladda upp en bild på din karaktär. 
			</p>
			<div class="question">
				<label for="Person">Deltagare</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">Vilken deltagare spelar karaktären?</div>
				<?php 
				if (is_null($role->Id)) {
				    if (count($current_persons)==1) {
				        echo "<tr><td>";
				        echo htmlspecialchars($current_persons[0]->Name) . "<br>\n";
				        echo "<input type='hidden' id='Person" . $current_persons[0]->Id . "' name='PersonId' value=" .  $current_persons[0]->Id . ">";
				        echo "</td>";
				        if ($current_persons[0]->getAgeAtLarp($current_larp) < $current_larp->smallChildAge()) $role->NoIntrigue = 1;
				    } else {
				        foreach ($current_persons as $current_person) {
				        echo "<tr>";
				        echo "<td>";
				        echo "<input type='radio' id='Person".$current_person->Id . "' name='PersonId' value='" . $current_person->Id . "' 'required'";
				        if ($current_person->getAgeAtLarp($current_larp) < $current_larp->smallChildAge()) {
				            echo " onchange='defaultMyslajvare()' ";
				        } else {
				            echo " onchange='defaultEjMyslajvare()' ";
				            
				        }
				        echo ">\n";
				        echo "<label for='Person" .$current_person->Id . "'>" .  htmlspecialchars($current_person->Name) . "</label><br>\n";
				        echo "</td>";
				        
				        echo "</tr>";
				    }
				    }
				    
				} else selectionByArray('Person', $current_persons, false, true, $role->PersonId); 
				
				?>
			</div>

			<div class="question">
				<label for="Name">Karaktärens namn</label>&nbsp;<font style="color:red">*</font>
				<br> <input class="input_field" type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($role->Name); ?>" size="100" maxlength="40" required>
			</div>
			<div class="question">
				<label for="Profession">Yrke</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">
					Vad jobbar din karaktär med för att överleva? 
					Vill du ha ett yrke som kan innebära en central karaktär i lajvet, så vill vi helst att du först kontaktar arrangörerna innan du anmäler den.
					  
					Har din karaktär tidigare haft en viktigare post har du naturligtvis oftast förtur till att få fortsätta spela att din karaktär har det yrket. 
					
				</div>
				<input class="input_field" type="text" id="Profession" name="Profession" value="<?php echo htmlspecialchars($role->Profession); ?>"  size="30" maxlength="50" required>
			</div>


			<div class="question">
				<label for="Description">Beskrivning</label>&nbsp;<font style="color:red">*</font><br> 
				<div class="explanation">Beskriv allt om din karaktär som arrangörerna behöver veta.<br>
				                         Allt som din karaktär har råkat ut för är sådan som kan påverka händelser i karaktärens framtid. 
				                         Spelledningen försöker hitta på saker baserat på vad din karaktär har råkat ut för så 
				                         att du därmed får en intressantare lajvupplevelse.</div>
				<textarea class="input_field" id="Description" name="Description" rows="4" cols="100" maxlength="15000" required><?php echo htmlspecialchars($role->Description); ?></textarea>
			</div>
			
			<div class="question">
				<label for="DescriptionForGroup">Beskrivning för din grupp</label><br> 
				<div class="explanation">Vad vet din grupp om dig? Skriv så mycket du kan så att ni kan lära känna varandra i gruppen innan lajvet börjar. 
										 Gärna roliga anekdoter från förr. Och vad de i gruppen gillar med dig, eller inte gillar.
									     Ju mer ni vet om varandra desto roligare spel kan ni få i gruppen.<br><br>
									     Efter att du är anmäld kan du gå in och titta på gruppen så får du se de andra som är anmälda och vad de har skrivit om sig.
									     </div>

				<textarea class="input_field" id="DescriptionForGroup" name="DescriptionForGroup" rows="4" cols="100" maxlength="15000"><?php echo htmlspecialchars($role->DescriptionForGroup); ?></textarea>
			</div>
			
			<div class="question">
				<label for="DescriptionForOthers">Beskrivning för andra</label><br> 
				<div class="explanation">Vad är allmänt känt om dig? Beskriv sådant som de flesta vet om dig. 
				                         Ju mer du skriver deso troligare är det att andra kan hitta beröringspunkter mellan dig och då får du roligare spel.<br><br>
									     Efter att du är anmäld och din karaktär är godkänd kommer den här beskrivningen 
									     att synas för alla andra som är anmälda och godkända.</a>. 
									     Lägg gärna upp en bild på dig också så att de andra känner igen dig.</div>
				<textarea class="input_field" id="DescriptionForOthers" name="DescriptionForOthers" rows="4" cols="100" maxlength="400"><?php echo htmlspecialchars($role->DescriptionForOthers); ?></textarea>
			</div>
			
				
				
			<div class="question">
				<label for="GroupsId">Vilken grupp är karaktären med i?</label><br>
       			<div class="explanation">Finns inte din grupp med på anmälan ska du kontakta den som är ansvarig för din grupp och se till att den är anmäld innan du själv anmäler dig.    
Efter att gruppen är anmäld måste den godkännas av arrangörerna innan den syns här. Om det är ett tag sedan gruppen anmäldes och den fortfarande inte syns får gruppledaren kontakta arrangörerna.<br>Anmäl dig bara till en grupp om du har fått ok på det från gruppansvarig. Om du vill skapa en egen grupp gör du det i det <a href="group_form.php">här formuläret</a>.
<br><br>
Om gruppen saknas kan du fortfarande spara din karaktär. Men du <strong>måste</strong> då ändra den efter att gruppen är anmäld och innan du anmäler dig så att karaktären kommer med i gruppen. Ändra den gör du genom att du klickar på namnet på karaktären från huvudsidan.
</div>
                <?php selectionByArray('Group', Group::getAllRegisteredApproved($current_larp), false, false, $role->GroupId); ?>
            </div>
				
				
			<?php  if (Race::isInUse($current_larp)) {?>	
			<div class="question">
				<label for="RaceId">Vilken typ av varelse är du?</label>&nbsp;<font style="color:red">*</font><br>
       			
                <?php Race::selectionDropdown($current_larp, false, true, $role->RaceId); ?>
            </div>
				<div class="question">
					<label for="RaceComment">Specificera din varelse/ras närmare om du vill.</label>
				<div class="explanation">Exempelvis vilken typ av svartblod, troll eller alv du spelar.</div>
					<br> <input class="input_field" type="text" id="RaceComment" value="<?php echo htmlspecialchars($role->RaceComment); ?>" name="RaceComment"  size="100" maxlength="200">
				</div>
			<?php } ?>	
				
				
			<div class="question">
				<label for="NoIntrigue">Vill du hålla dig i bakgrunden?</label><br>
       			<div class="explanation">Vill du lajva i bakgrunden, alltså inte få några skrivna intriger eller bli involverad i andras intriger? 
       			Du åker främst för att uppleva stämningen och finnas i bakgrunden, utan att vara en aktiv del av lajvet.
			</div>

        	<input type="radio" id="myslajvare_yes" name="NoIntrigue" value="1" onclick="handleRadioClick()" <?php if ($role->isMysLajvare()) echo 'checked="checked"'?>>
        	<label for="myslajvare_yes">Ja</label><br>
        	<input type="radio" id="myslajvare_no" name="NoIntrigue" value="0" onclick="handleRadioClick()"<?php if (!$role->isMysLajvare()) echo 'checked="checked"'?>>
        	<label for="myslajvare_no">Nej</label><br>

								
           </div>
			<?php  if (LarperType::isInUse($current_larp)) {?>	
			<div class="question intrigue">
				<label for="LarperTypesId">Vilken typ av lajvare är du?</label>&nbsp;<font style="color:red">*</font><br>
       			<div class="explanation">Tänk igenom ditt val noga. Det är det här som i första hand kommer 
       			att avgöra hur mycket energi arrangörerna kommer lägga ner på dina intriger.     
       			Är du ny på lajv? Vi rekommenderar då att du att du väljer ett alternativ som ger mycket intriger. 
       			Erfarenhetsmässigt brukar man som ny lajvare ha mer nytta av mycket intriger än en 
       			erfaren lajvare som oftast har enklare hitta på egna infall under lajvet.   
       			</div>
                <?php LarperType::selectionDropdown($current_larp, false, true, $role->LarperTypeId); ?>
            </div>
				<div class="question intrigue">
				<label for="TypeOfLarperComment">Kommentar till typ av lajvare</label>
				<div class="explanation">Exempel:<br>
				                         Jag är passiv lajvare, men kan tänka mig aktiva intriger som rör X, Y och Z.<br>
				                         Jag är karaktärslajvare och klarar mig utan intriger, men om ni har tankar om saker min karaktär kann vara involverad i som ger spel åt andra kan jag vara intresserad.<br>
				                         Jag är aktiv lajvare, men vill helst undvika våldsamma intriger.<br>
				                         Jag är action-lajvare och vill spela den sökta karaktären NN.<br>
				                         Jag är action-lajvare och har inget emot en våldsam död.</div>
					<br> <input class="input_field" type="text" id="TypeOfLarperComment" value="<?php echo htmlspecialchars($role->TypeOfLarperComment); ?>" name="TypeOfLarperComment"  size="100" maxlength="200">
				</div>
			<?php } ?>	
				
			<?php  if (Ability::isInUse($current_larp)) {?>	
			<div class="question intrigue">
				<label for="AbilityId">Har din karaktär någon av följande kunskaper?</label><br>
       			<div class="explanation">Specialförmågor är sådant som påverkar speltekniken. Exempelvis "tål ett extra slag", men inte "har bättre luktsinne". Om du ska vara magi- eller alkemikunnig ska du innan din anmälan fått detta godkänt av magi-/alkemiarrangör, via e-post doh@berghemsvanner.se.
Nyheter i regelsystemen för alkemi och magi kommer upp på hemsidan och facebook-sidan.   
       			</div>
                <?php 
                selectionByArray('Ability' , Ability::allActive($current_larp), true, false, $role->getSelectedAbilityIds());
                ?>
            </div>
				<div class="question intrigue">
				<label for="AbilityComment">Om du valde specialstyrka/-förmåga, specificera och beskriv den/dessa så utförligt som möjligt.</label>
				<div class="explanation">Motivera varför du har denna specialstyrka/-förmåga väl.</div>
					<br> <input class="input_field" type="text" id="AbilityComment" value="<?php echo htmlspecialchars($role->AbilityComment); ?>" name="AbilityComment"  size="100" maxlength="200">
				</div>
			<?php } ?>	

			<div class="question intrigue">
				<label for="Birthplace">Var är karaktären född?</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">Beskriv platsen om den inte är känd eller om den är relevant för rollen.</div>
				<input class="input_field requiredIntrigueField" type="text" id="Birthplace" name="Birthplace" value="<?php echo htmlspecialchars($role->Birthplace); ?>"  size="100" maxlength="100" required>
			</div>
			<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
			<div class="question intrigue">
			<label for="PlaceOfResidence">Var bor karaktären?</label>&nbsp;<font style="color:red">*</font>
			<div class="explanation">Tänk typ folkbokföringsadress, dvs även om karaktären tillfälligt är på platsen så vill vi veta var karaktären har sitt hem.<br>
			   </div>
			
			
            <?php
            PlaceOfResidence::selectionDropdown($current_larp, false, true, $role->PlaceOfResidenceId);
            ?> 

			</div>
			<?php }?>
			<div class="question intrigue">
				<label for="CharactersWithRelations">Relationer med andra</label><br> 
				<div class="explanation">Tre karaktärer (på lajvet eller som bakgrundskaraktärer) som är viktiga för din karaktär och mycket kort hur vi kan ge spel på dessa karaktärer.</div>
				<textarea class="input_field" id="CharactersWithRelations" name="CharactersWithRelations" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->CharactersWithRelations); ?></textarea>
			</div>
			
			
			
			
			
				
			<div class="question intrigue">
				<label for="ReasonForBeingInSlowRiver">Varför befinner sig karaktären på platsen?</label>&nbsp;<font style="color:red">*</font><br> 
				<div class="explanation">Varför är din roll på plats? Är hen bosatt och i så fall sedan hur länge? Har hen en anledning att besöka just nu? Brukar hen besöka platsen?</div>
				<textarea class="input_field requiredIntrigueField" id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100" maxlength="60000" required><?php echo htmlspecialchars($role->ReasonForBeingInSlowRiver); ?></textarea>
			</div>
				
			<?php if ($campaign->is_kir()) { ?>
			<div class="question intrigue">
				<label for="PreviousLarps">Tidigare lajv</label><br> 
				<div class="explanation"><?php echo $campaign->Name ?> är en kampanj. Det innebär att allt din karaktär gör ett år och andra gör mot den ska påverka det här lajvet.    
					Det är inte så farligt som det låter, utan är ett bra sätt att ge större djup i lajvet och göra din egen karaktär intressantare både för dig själv och andra.<br><br>
					Om du var med förra året med din karaktär, vad hände med din karaktär som är bra att komma ihåg? Gjorde den några särskilt bra affärer? Var den med i en duell? Blev den svindlad eller svindlade den någon? Hur gick det med kärleken?<br><br>
					Har din karaktär gjort något minnesvärt tidigare år?
				</div>
				<textarea class="input_field" id="PreviousLarps" name="PreviousLarps" rows="8" cols="100" maxlength="15000"><?php echo htmlspecialchars($role->PreviousLarps); ?></textarea>
			</div>
			<?php }?>
			
			<?php if (Religion::isInUse($current_larp)) {?>
			<div class="question intrigue">
				<label for="ReligionId">Vilken religion har karaktären?</label>&nbsp;<font style="color:red">*</font><br>
       			<div class="explanation">Vissa religioner har bättre anseende än andra. Religionen kommer att påverka spel. </div>
                <?php Religion::selectionDropdown($current_larp, false,true, $role->ReligionId); ?>
            </div>

			<div class="question intrigue">
				<label for="Religion">Religion förklaring</label><br>
				<div class="explanation"> Skriv så noggrant du kan om du inte har en "vanlig" religion.</div>
				<input class="input_field" type="text" id="Religion" name="Religion" value="<?php echo htmlspecialchars($role->Religion); ?>"  size="100" maxlength="200">
			</div>

			<?php } ?>	

			<?php if (Belief::isInUse($current_larp)) {?>
			<div class="question intrigue">
				<label for="BeliefId">Hur troende är karaktären?</label>&nbsp;<font style="color:red">*</font><br>
       			<div class="explanation">För vissa är religionen viktigare än andra. Hur är det för din karaktär?</div>
                <?php Belief::selectionDropdown($current_larp, false,true, $role->BeliefId); ?>
            </div>
			<?php } ?>	
				

			<?php if (Wealth::isInUse($current_larp)) {?>
			<div class="question intrigue">
				<label for="WealthsId">Hur rik är karaktären?</label>&nbsp;<font style="color:red">*</font><br>
       			<div class="explanation">Om du anser att du har rikedom 3 eller högre förväntas du i regel ha någon form av affärer på gång. 
       			Det kan vara att sälja saker din gård producerat, leta guld eller nästan vad som helst som gör att man inte är fattig längre.   
       			Det kommer att vara ett begränsat antal stenrika på lajvet och vi godkänner i regel inte nya. 
       			Undantag kan naturligtvis förekomma om det gynnar lajvet.   
       			Däremot är <?php echo $campaign->Name ?> en kampanj så det går att spela sig till att bli stenrik. 
       			Det går också att bli fattig om man är stenrik.</div>
                <?php Wealth::selectionDropdown($current_larp, false,true, $role->WealthId); ?>
            </div>
			<?php } ?>	

			<?php  if (RoleFunction::isInUse($current_larp)) {?>	
			<div class="question intrigue">
				<label for="RoleFunctionId">Har din karaktär någon särskild funktion eller syssla på lajvet?</label>&nbsp;<font style="color:red">*</font><br>
       			<div class="explanation">   
       			</div>
                <?php 
                selectionByArray('RoleFunction' , RoleFunction::allActive($current_larp), true, false, $role->getSelectedRoleFunctionIds());
                ?>
            </div>
            
			<div class="question intrigue">
				<label for="RoleFunctionComment">Funktioner förklaring</label><br>
				<div class="explanation">Vill du förtydliga något kring funktioner eller lägga till något?</div>
				<input class="input_field" type="text" id="RoleFunctionComment" name="RoleFunctionComment" value="<?php echo htmlspecialchars($role->RoleFunctionComment); ?>"  size="100" maxlength="200">
			</div>
			<?php } ?>	

			<div class="question intrigue">
				<label for="IntrigueSuggestions">Intrigideer</label><br> 
				<div class="explanation">Är det någon typ av spel du särskilt önskar eller något som du inte önskar spel på?  Exempel kan vara "Min karaktär har: en skuld till en icke namngiven karaktär/mördat någon/svikit sin familj/ett oäkta barn/lurat flera personer på pengar". </div>
				<textarea class="input_field" id="IntrigueSuggestions" name="IntrigueSuggestions" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->IntrigueSuggestions); ?></textarea>
			</div>
			<?php if (IntrigueType::isInUseForRole($current_larp)) {?>
			<div class="question intrigue">
				<label for="IntrigueTypeId">Intrigtyper</label><br> 
				<div class="explanation">Vilken typ av intriger vill du ha?
				
				</div>
				<?php 
				IntrigueType::selectionDropdownRole($current_larp, true, false, $role->getSelectedIntrigueTypeIds());
				
				?>
			</div>
			<?php } ?>
			<div class="question intrigue">
				<label for="NotAcceptableIntrigues">Saker karaktären absolut inte vill spela på</label><br>
				<div class="explantion">Är det något du som deltagare inte vill spela på? Du kommer inte få intriger som berör detta, även om vi inte kan garantera att du slipper det helt på lajvet. Detta då vi inte kan kontrollera allt som händer på lajvet.</div>
				<input class="input_field" type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($role->NotAcceptableIntrigues); ?>"  size="100" maxlength="200">
			</div>


			<div class="question intrigue">
				<label for="DarkSecret">Karaktärens (mörka) baksida</label>&nbsp;<font style="color:red">*</font><br> 
				<div class="explanation">Har din karaktär någon mörk hemlighet eller något personlighetsdrag som är till dennes nackdel? Har hen en hemlig skuld, ett hetsigt humör som sätter den i problem eller har hen kanske begått något brott? Det kan kännas svårt att göra karaktären sårbar på det här sättet, men försök. En mer nyanserad karaktär är ofta roligare och mer spännande att spela. </div>
				<textarea class="input_field requiredIntrigueField" id="DarkSecret" name="DarkSecret" rows="4" cols="100" maxlength="60000" required><?php echo htmlspecialchars($role->DarkSecret); ?> </textarea>
			</div>
			<div class="question intrigue">
				<label for="DarkSecretIntrigueIdeas">Karaktärens (mörka) baksida - intrigidéer</label>&nbsp;<font style="color:red">*</font><br>
				<div class="explanation">Hur kan vi spela på karaktärens mörka baksida?</div>
				<input class="input_field requiredIntrigueField" type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo htmlspecialchars($role->DarkSecretIntrigueIdeas); ?>"  size="100" maxlength="200" required>
			</div>





			<div class="question">
				<label for="OtherInformation">Övrig information</label><br>
				<div class="explanation">Är det något annat kring karaktären arrangörerna bör veta?</div>
				<textarea class="input_field" id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->OtherInformation); ?></textarea>
			
			 
			</div>


			<?php 
			if ($admin) {
			    //Om bara tittar på formuläret som arrangör får man inte lyckas skicka in
			    $type = "button";
			} else {
			    $type = "submit";
		    }
		    
			    ?>


			<input type='<?php echo $type ?>' value='<?php echo default_value('action') ?>'>
		</form>
	</div>



<script>

	<?php 
	if($role->isMysLajvare()) {
	    echo 'setFieldState(true);';
	} else {
	    echo 'setFieldState(false);';
	}
	?>



</script>


</body>
</html>