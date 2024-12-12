<?php

require 'header.php';

$admin = false;
if (isset($_GET['admin'])) $admin = true;



if (empty($current_person) && !$admin) {
    header('Location: index.php');
    exit;
}

$role = Role::newWithDefault();
$role->PersonId = $current_person->Id;

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

if ($operation == 'update' && $role->PersonId != $current_person->Id) {
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
                $output = "Skapa";
                break;
            }
            $output = "Ändra";
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

	<div class='itemselector'>
	<div class="header">

		<i class="fa-solid fa-person"></i>
		<?php 
		if ($operation == 'update') {
		    echo "Ändra $role->Name";
		} else {
		    echo "Skapa karaktär";
		}    
		 ?>	
	 </div>
	<div class='itemcontainer'>
	Vi vill veta vilken karaktär du vill spela.<br />
	Om du vill spela en av lajvets sökta karaktärer ber vi dig att kontakta arrangörerna innan du fyller i din anmälan.<br />
	Tänk på att din karaktär också måste godkännas av arrangörerna.   <br>
	<br>
	Efter anmälan kommer du att kunna ladda upp en bild på din karaktär. 
	</div>
	
	<form action="logic/role_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $role->PersonId; ?>">

    	<div class='itemcontainer'>
       	<div class='itemname'>Karaktärens namn <font style="color:red">*</font></div>
    	<input type="text" id="Name" name="Name" value="<?php echo htmlspecialchars($role->Name); ?>" maxlength="40" required>
    	</div>
			
    	<div class='itemcontainer'>
       	<div class='itemname'>Yrke <font style="color:red">*</font></div>
       	Vad jobbar din karaktär med för att överleva?<br> 
		Vill du ha ett yrke som kan innebära en central karaktär i lajvet, 
		så vill vi helst att du först kontaktar arrangörerna innan du anmäler den.<br>
		Har din karaktär tidigare haft en viktigare post har du naturligtvis oftast förtur till att få fortsätta 
		spela att din karaktär har det yrket. <br>
    	<input type="text" id="Profession" name="Profession" value="<?php echo htmlspecialchars($role->Profession); ?>" maxlength="50" required>
    	</div>

    	<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning <font style="color:red">*</font></div>
       	Beskriv allt om din karaktär som arrangörerna behöver veta.<br>
         Allt som din karaktär har råkat ut för är sådan som kan påverka händelser i karaktärens framtid. 
         Spelledningen försöker hitta på saker baserat på vad din karaktär har råkat ut för så 
         att du därmed får en intressantare lajvupplevelse. <br>
    	<textarea id="Description" name="Description" rows="4" maxlength="15000" required><?php echo htmlspecialchars($role->Description); ?></textarea>
    	</div>

    	<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning för din grupp</div>
       	Vad vet din grupp om dig? Skriv så mycket du kan så att ni kan lära känna varandra i gruppen innan lajvet börjar. 
		 Gärna roliga anekdoter från förr. Och vad de i gruppen gillar med dig, eller inte gillar.
	     Ju mer ni vet om varandra desto roligare spel kan ni få i gruppen.<br><br>
	     Efter att du är anmäld kan du gå in och titta på gruppen så får du se de andra som är anmälda och vad de har skrivit om sig. <br>
    	<textarea id="DescriptionForGroup" name="DescriptionForGroup" rows="4" maxlength="15000"><?php echo htmlspecialchars($role->DescriptionForGroup); ?></textarea>
    	</div>


    	<div class='itemcontainer'>
       	<div class='itemname'>Beskrivning för andra</div>
       	Vad är allmänt känt om dig? Beskriv sådant som de flesta vet om dig. 
         Ju mer du skriver deso troligare är det att andra kan hitta beröringspunkter mellan dig och då får du roligare spel.<br><br>
	     När du har en plats på lajvet kommer den här beskrivningen 
	     att synas för alla andra som har plats på lajvet. <br>
	     Lägg gärna upp en bild på dig också så att de andra känner igen dig.<br>
    	<textarea id="DescriptionForOthers" name="DescriptionForOthers" rows="4" maxlength="400"><?php echo htmlspecialchars($role->DescriptionForOthers); ?></textarea>
    	</div>

				
    	<div class='itemcontainer'>
       	<div class='itemname'>Vilken grupp är karaktären med i?</div>
       	Finns inte din grupp med på anmälan ska du kontakta den som är ansvarig för din grupp och se till att den är anmäld innan du själv anmäler dig.    
        Efter att gruppen är anmäld måste den godkännas av arrangörerna innan den syns här. Om det är ett tag sedan gruppen anmäldes och den 
        fortfarande inte syns får gruppledaren kontakta arrangörerna.<br>
        Anmäl dig bara till en grupp om du har fått ok på det från gruppansvarig. Om du vill skapa en egen grupp gör du det i 
        det <a href="group_form.php">här formuläret</a>.
        <br><br>
        Om gruppen saknas kan du fortfarande spara din karaktär. Men du <strong>måste</strong> då ändra den efter att gruppen är anmäld och 
        innan du anmäler dig så att karaktären kommer med i gruppen. Ändra den gör du genom att du klickar på 
        namnet på karaktären från huvudsidan.<br>
    	<?php selectionDropDownByArray('GroupId', Group::getAllRegisteredApproved($current_larp), false, false, $role->GroupId); ?>
    	</div>

				
		<?php  if (Race::isInUse($current_larp)) {?>	
       		<div class='itemcontainer'>
           	<div class='itemname'>Vilken typ av varelse är du? <font style="color:red">*</font></div>
        	<?php Race::selectionDropdown($current_larp, false, true, $role->RaceId); ?>
        	</div>
			
      		<div class='itemcontainer'>
           	<div class='itemname'>Specificera din varelse/ras närmare om du vill.</div>
           	Exempelvis vilken typ av svartblod, troll eller alv du spelar.<br>
        	<input type="text" id="RaceComment" value="<?php echo htmlspecialchars($role->RaceComment); ?>" name="RaceComment"  size="100" maxlength="200">
        	</div>
		<?php } ?>	
				
  		<div class='itemcontainer'>
       	<div class='itemname'>Vill du hålla dig i bakgrunden?</div>
       	Vill du lajva i bakgrunden, alltså inte få några skrivna intriger eller bli involverad i andras intriger? 
		Du åker främst för att uppleva stämningen och finnas i bakgrunden, utan att vara en aktiv del av lajvet.<br>
    	<input type="radio" id="myslajvare_yes" name="NoIntrigue" value="1" onclick="handleRadioClick()" <?php if ($role->isMysLajvare()) echo 'checked="checked"'?>>
    	<label for="myslajvare_yes">Ja</label><br>
    	<input type="radio" id="myslajvare_no" name="NoIntrigue" value="0" onclick="handleRadioClick()"<?php if (!$role->isMysLajvare()) echo 'checked="checked"'?>>
    	<label for="myslajvare_no">Nej</label>
    	</div>
				
		<?php  if (LarperType::isInUse($current_larp)) {?>
      		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Vilken typ av lajvare är du?&nbsp;<font style="color:red">*</font></div>
           	Tänk igenom ditt val noga. Det är det här som i första hand kommer 
       			att avgöra hur mycket energi arrangörerna kommer lägga ner på dina intriger.     
       			Är du ny på lajv? Vi rekommenderar då att du att du väljer ett alternativ som ger mycket intriger. 
       			Erfarenhetsmässigt brukar man som ny lajvare ha mer nytta av mycket intriger än en 
       			erfaren lajvare som oftast har enklare hitta på egna infall under lajvet.<br>
        	<?php LarperType::selectionDropdown($current_larp, false, true, $role->LarperTypeId); ?>
        	</div>

      		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Kommentar till typ av lajvare</div>
           	Exempel:<br>
             Jag är passiv lajvare, men kan tänka mig aktiva intriger som rör X, Y och Z.<br>
             Jag är karaktärslajvare och klarar mig utan intriger, men om ni har tankar om saker min karaktär kann vara involverad i som ger spel åt andra kan jag vara intresserad.<br>
             Jag är aktiv lajvare, men vill helst undvika våldsamma intriger.<br>
             Jag är action-lajvare och vill spela den sökta karaktären NN.<br>
             Jag är action-lajvare och har inget emot en våldsam död.<br>
        	<input type="text" id="TypeOfLarperComment" value="<?php echo htmlspecialchars($role->TypeOfLarperComment); ?>" name="TypeOfLarperComment"  size="100" maxlength="200">
        	</div>
		<?php } ?>	
				
		<?php  if (Ability::isInUse($current_larp)) {?>	
      		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Specialförmågor är sådant som påverkar speltekniken. Exempelvis "tål ett extra slag", men inte "har bättre luktsinne". Om du ska vara magi- eller alkemikunnig ska du innan din anmälan fått detta godkänt av magi-/alkemiarrangör, via e-post doh@berghemsvanner.se.
Nyheter i regelsystemen för alkemi och magi kommer upp på hemsidan och facebook-sidan.<br>
        	<?php selectionByArray('Ability' , Ability::allActive($current_larp), true, false, $role->getSelectedAbilityIds()); ?>
        	</div>

      		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Om du valde specialstyrka/-förmåga, specificera och beskriv den/dessa så utförligt som möjligt.</div>
           	Motivera varför du har denna specialstyrka/-förmåga väl.<br>
        	<input type="text" id="AbilityComment" value="<?php echo htmlspecialchars($role->AbilityComment); ?>" name="AbilityComment"  size="100" maxlength="200">
        	</div>
		<?php } ?>	

 		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Var är karaktären född?&nbsp;<font style="color:red">*</font></div>
       	Beskriv platsen om den inte är känd eller om den är relevant för rollen.<br>
    	<input class="requiredIntrigueField" type="text" id="Birthplace" name="Birthplace" value="<?php echo htmlspecialchars($role->Birthplace); ?>"  size="100" maxlength="100" required>
    	</div>


		<?php if (PlaceOfResidence::isInUse($current_larp)) {?>
    		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Var bor karaktären?&nbsp;<font style="color:red">*</font></div>
           	Tänk typ folkbokföringsadress, dvs även om karaktären tillfälligt är på platsen så vill vi veta var karaktären har sitt hem.<br>
        	<?php PlaceOfResidence::selectionDropdown($current_larp, false, true, $role->PlaceOfResidenceId); ?> 
        	</div>
		<?php }?>
		
 		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Relationer med andra</div>
       	Tre karaktärer (på lajvet eller som bakgrundskaraktärer) som är viktiga för din karaktär och mycket kort hur vi kan ge spel på dessa karaktärer.<br>
    	<textarea id="CharactersWithRelations" name="CharactersWithRelations" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->CharactersWithRelations); ?></textarea>
    	</div>

 		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Varför befinner sig karaktären på platsen?&nbsp;<font style="color:red">*</font></div>
       	Varför är din roll på plats? Är hen bosatt och i så fall sedan hur länge? Har hen en anledning att besöka just nu? Brukar hen besöka platsen?<br>
    	<textarea class="requiredIntrigueField" id="ReasonForBeingInSlowRiver" name="ReasonForBeingInSlowRiver" rows="4" cols="100" maxlength="60000" required><?php echo htmlspecialchars($role->ReasonForBeingInSlowRiver); ?></textarea>
    	</div>
			
		<?php if (Religion::isInUse($current_larp)) {?>
     		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Vilken religion har karaktären?&nbsp;<font style="color:red">*</font></div>
           	Vissa religioner har bättre anseende än andra. Religionen kommer att påverka spel. <br>
        	<?php Religion::selectionDropdown($current_larp, false,true, $role->ReligionId); ?>
        	</div>

    		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Religion förklaring</div>
           	Skriv så noggrant du kan om du inte har en "vanlig" religion.<br>
        	<input type="text" id="Religion" name="Religion" value="<?php echo htmlspecialchars($role->Religion); ?>"  size="100" maxlength="200">
        	</div>
		<?php } ?>	

		<?php if (Belief::isInUse($current_larp)) {?>
     		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Hur troende är karaktären?&nbsp;<font style="color:red">*</font></div>
           	För vissa är religionen viktigare än andra. Hur är det för din karaktär? <br>
        	<?php Belief::selectionDropdown($current_larp, false,true, $role->BeliefId); ?>
        	</div>
		<?php } ?>	
				

		<?php if (Wealth::isInUse($current_larp)) {?>
     		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Hur rik är karaktären?&nbsp;<font style="color:red">*</font></div>
           	Om du är rik har du i regel ha någon form av affärer på gång. <br>
   			Det kommer att vara ett begränsat antal stenrika på lajvet och vi godkänner i regel inte nya. 
   			Undantag kan naturligtvis förekomma om det gynnar lajvet.   
   			Däremot är <?php echo $campaign->Name ?> en kampanj så det går att spela sig till att bli stenrik. 
   			Precis som det går att spela sig till rikedom går det att spela sig till att bli fattigare.<br>
        	<?php Wealth::selectionDropdown($current_larp, false,true, $role->WealthId); ?>
        	</div>
		<?php } ?>	

		<?php  if (RoleFunction::isInUse($current_larp)) {?>	
     		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Har din karaktär någon särskild funktion eller syssla på lajvet?</div>
        	<?php selectionByArray('RoleFunction' , RoleFunction::allActive($current_larp), true, false, $role->getSelectedRoleFunctionIds()); ?>
        	</div>

    		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Funktioner förklaring</div>
           	Vill du förtydliga något kring funktioner eller lägga till något?<br>
        	<input type="text" id="RoleFunctionComment" name="RoleFunctionComment" value="<?php echo htmlspecialchars($role->RoleFunctionComment); ?>"  size="100" maxlength="200">
        	</div>
		<?php } ?>	

 		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Intrigideer</div>
       	Är det någon typ av spel du särskilt önskar eller något som du inte önskar spel på?  Exempel kan vara "Min karaktär har: en skuld till en icke namngiven karaktär/mördat någon/svikit sin familj/ett oäkta barn/lurat flera personer på pengar."<br>
    	<textarea id="IntrigueSuggestions" name="IntrigueSuggestions" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->IntrigueSuggestions); ?></textarea>
    	</div>
			

		<?php if (IntrigueType::isInUseForRole($current_larp)) {?>
     		<div class='itemcontainer intrigue'>
           	<div class='itemname'>Intrigtyper</div>
           	Vilken typ av intriger vill du ha?<br>
        	<?php IntrigueType::selectionDropdownRole($current_larp, true, false, $role->getSelectedIntrigueTypeIds()); ?>
        	</div>
		<?php } ?>
		
		
		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Saker karaktären absolut inte vill spela på</div>
       	Är det något karaktären aldrig skulle göra?<br>
    	<input type="text" id="NotAcceptableIntrigues" name="NotAcceptableIntrigues" value="<?php echo htmlspecialchars($role->NotAcceptableIntrigues); ?>"  size="100" maxlength="200">
    	</div>
		
		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Karaktärens (mörka) baksida&nbsp;<font style="color:red">*</font></div>
       	Har din karaktär någon mörk hemlighet eller något personlighetsdrag som är till dennes nackdel? 
       	Har hen en hemlig skuld, ett hetsigt humör som sätter den i problem eller har hen kanske begått något brott? 
       	Det kan kännas svårt att göra karaktären sårbar på det här sättet, men försök. 
       	En mer nyanserad karaktär är ofta roligare och mer spännande att spela.<br>
    	<textarea class="requiredIntrigueField" id="DarkSecret" name="DarkSecret" rows="4" maxlength="60000" required><?php echo htmlspecialchars($role->DarkSecret); ?> </textarea>
    	</div>

		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Karaktärens (mörka) baksida - intrigidéer&nbsp;<font style="color:red">*</font></div>
       	Hur kan vi spela på karaktärens mörka baksida?<br>
    	<input class="requiredIntrigueField" type="text" id="DarkSecretIntrigueIdeas" name="DarkSecretIntrigueIdeas" value="<?php echo htmlspecialchars($role->DarkSecretIntrigueIdeas); ?>"  size="100" maxlength="200" required>
    	</div>

		<div class='itemcontainer intrigue'>
       	<div class='itemname'>Övrig information</div>
       	Är det något annat kring karaktären arrangörerna bör veta?<br>
    	<textarea id="OtherInformation" name="OtherInformation" rows="4" cols="100" maxlength="60000"><?php echo htmlspecialchars($role->OtherInformation); ?></textarea>
    	</div>

			<?php 
			if ($admin) {
			    //Om bara tittar på formuläret som arrangör får man inte lyckas skicka in
			    $type = "button";
			} else {
			    $type = "submit";
		    }
		    
			    ?>


			<div class='center'><input type='<?php echo $type ?>' class='button-18' value='<?php echo default_value('action') ?>'></div>
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