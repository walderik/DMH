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
$role->CreatorPersonId = $current_person->Id;
$isPc = true;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $operation = "insert";
    
    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
    }
    else {

    }
    if ($operation == 'insert') {
        if (isset($_GET['type'])) {
            if ($_GET['type'] == "npc") {
                $role->PersonId = NULL;
                $isPc = false;
            }
            if (!$isPc && isset($_GET['groupId'])) $role->GroupId = $_GET['groupId'];
            
        }
    } elseif ($operation == 'update') {
        $role = Role::loadById($_GET['id']);
        $isPc = $role->isPC($current_larp);
    } else {
    }
}



if (!$role->userMayEdit()) {
    header('Location: ' . $role->getViewLink());
    exit;
}



if ($isPc && $operation == 'update' && $role->PersonId != $current_person->Id) {
    header('Location: index.php'); //Inte din karaktär
    exit;
}

$group = $role->getGroup();
if (!$isPc && !empty($group) && !$current_person->isMemberGroup($group) && !$current_person->isGroupLeader($group)) {
    header('Location: index.php'); //NPC som inte är med i din grupp
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
	<?php if ($isPc) { ?>
	Vi vill veta vilken karaktär du vill spela.<br />
	Om du vill spela en av lajvets sökta karaktärer ber vi dig att kontakta arrangörerna innan du fyller i din anmälan.<br />
	Tänk på att din karaktär också måste godkännas av arrangörerna.   <br>
	<br>
	Efter anmälan kommer du att kunna ladda upp en bild på din karaktär. 
	<?php } else {?>
	Bekriv NPC'n så noggrant som möjligt. Dels för att alla i gruppen ska kunna veta vem det är och dels för att arrangörena ska få reda på vem det är.<br>
	Precis som med spelade karaktärer krävs det att NPC'n är godkända av arrangör. 
	<?php }?>
	</div>
	
	<form action="logic/role_form_save.php" method="post">
		<input type="hidden" id="operation" name="operation" value="<?php default_value('operation'); ?>"> 
		<input type="hidden" id="Id" name="Id" value="<?php echo $role->Id; ?>">
		<?php if ($isPc) { ?>
		<input type="hidden" id="PersonId" name="PersonId" value="<?php echo $role->PersonId; ?>">
		<?php }  ?>
		<input type="hidden" id="CreatorPersonId" name="CreatorPersonId" value="<?php echo $role->CreatorPersonId; ?>">
		    

		<?php 
		print_participant_text_input(
		    "Karaktärens namn",
		    "",
		    "Name",
		    $role->Name,
		    "maxlength='40'",
		    true,
		    false);
		
		if (!$campaign->is_hfs()) {
    		$description = "Vad jobbar karaktären med för att överleva?";
    		if ($isPc) {
    		    $description.="<br>".
    		  		    "Vill du ha ett yrke som kan innebära en central karaktär i lajvet, 
    		          så vill vi helst att du först kontaktar arrangörerna innan du anmäler den.<br>
    		          Har din karaktär tidigare haft en viktigare post har du naturligtvis oftast förtur till att få fortsätta 
    		          spela att din karaktär har det yrket. ";
    		}
    		print_participant_text_input(
    		    "Yrke",
    		    $description,
    		    "Profession",
    		    $role->Profession,
    		    "maxlength='50'",
    		    true,
    		    false);
		}
		
		if ($isPc) {
           $description = "Beskriv allt om karaktären som arrangörerna behöver veta.<br>
            Allt som karaktären har råkat ut för är sådan som kan påverka händelser i karaktärens framtid.
            Spelledningen försöker hitta på saker baserat på vad din karaktär har råkat ut för så 
            att du därmed får en intressantare lajvupplevelse.";
    	} else {
    	    $description = "Beskriv allt om karaktären som arrangörerna behöver veta.<br>
            Här vill vi att ni skriver allt som rör karaktären, vad för egenskaper denne har, hemliga agendor, Vad driver rollen, 
    	    vad det är du/ni vill att denne är bra/dålig på osv. Detta för att vi som arrangörer skall förstå karaktären. 
    	    Glöm inte heller att skriva vad för off:önskemål/tankar som finns kring karaktären.";
     	}
     	
	  print_participant_textarea(
	      "Beskrivning",
	      $description,
	      "Description",
	      $role->Description,
	      "rows='4' maxlength='15000'",
	      true,
	      false);


	  if ($campaign->hasGroups()) {
    		$description = "Vad vet gruppen om karaktären? Skriv så mycket du kan så att ni kan lära känna varandra i gruppen innan lajvet börjar.
    		      Gärna roliga anekdoter från förr. Och vad de i gruppen gillar med karaktären, eller inte gillar.
    		      Ju mer ni vet om varandra desto roligare spel kan ni få i gruppen.";
    		if ($isPc) {
    	       $description .= "<br><br>Efter att du är anmäld kan du gå in och titta på gruppen så får du se de andra som är anmälda och vad de har skrivit om sig. ";
    		}
    		print_participant_textarea(
    		    "Beskrivning för gruppen",
    		    $description,
    		    "DescriptionForGroup",
    		    $role->DescriptionForGroup,
    		    "rows='4' maxlength='15000'",
    		    !$isPc,
    		    false);
	  }
		
	    $description = "Vad är allmänt känt om karaktären? Beskriv sådant som de flesta vet om eller kan ha hört om karaktären. 
            Ju mer du skriver deso troligare är det att andra kan hitta beröringspunkter mellan karaktärerna och då blir det roligare spel.";
	    if ($isPc) {
	        $description .= "<br><br>
                När du har en plats på lajvet kommer den här beskrivningen 
	           att synas för alla andra som har plats på lajvet. <br>
	           Lägg gärna upp en bild på dig också så att de andra känner igen dig.";
	    }
		
	    print_participant_textarea(
	        "Beskrivning för andra",
	        $description,
	        "DescriptionForOthers",
	        $role->DescriptionForOthers,
	        "rows='4' maxlength='400'",
	        false,
	        false);
	    
  
	    if ($campaign->hasGroups()) {
    	    if ($role->isNPC($current_larp)) {  
    		    echo "<input type='hidden' id='GroupId' name='GroupId' value='$role->GroupId'>";
    		} else {
    		$description = "Finns inte din grupp med på anmälan ska du kontakta den som är ansvarig för din grupp och se till att den är anmäld innan du själv anmäler dig.    
                    Efter att gruppen är anmäld måste den godkännas av arrangörerna innan den syns här. Om det är ett tag sedan gruppen anmäldes och den 
                    fortfarande inte syns får gruppledaren kontakta arrangörerna.<br>
                    Anmäl dig bara till en grupp om du har fått ok på det från gruppansvarig. Om du vill skapa en egen grupp gör du det i 
                    det <a href='group_form.php'>här formuläret</a>.
                    <br><br>
                    Om gruppen saknas kan du fortfarande spara din karaktär. Men du <strong>måste</strong> då ändra den efter att gruppen är anmäld och 
                    innan du anmäler dig så att karaktären kommer med i gruppen. Ändra den gör du genom att du klickar på 
                    namnet på karaktären från huvudsidan.";
    		    
    		$group = $role->getGroup();
    		    print_participant_question_start(
    		    	"Vilken grupp är karaktären med i?", 
    		    	$description, 
    		    	false, 
    		        false,
    		        empty($group));
    
            	
            	$groupName = "";
            	if (isset($group)) $groupName = $group->Name;
            	selectionDropDownByArray('GroupId', Group::getAllChoosable($current_larp), false, $role->GroupId, $groupName);
            	print_participant_question_end(false);
    
    		}
	    }
  
		if (Race::isInUse($current_larp)) {
		    print_participant_question_start(
		        "Vilken typ av varelse är karaktären?",
		        "",
		        true,
		        false,
		        empty($role->RaceId));
		    Race::selectionDropdown($current_larp, false, true, $role->RaceId); 
		    print_participant_question_end(true);
		    
			print_participant_text_input(
			    "Specificera karaktärens varelse/ras närmare om du vill",
			    "Exempelvis vilken typ av svartblod, troll eller alv du spelar",
			    "RaceComment",
			    $role->RaceComment,
			    "size='100' maxlength='200'",
			    false,
			    false);
		}

		if (!$campaign->is_hfs() && $isPc) { 
		    print_participant_question_start(
		        "Vill du hålla dig i bakgrunden?",
		        "Vill du lajva i bakgrunden, alltså inte få några skrivna intriger eller bli involverad i andras intriger? 
		Du åker främst för att uppleva stämningen och finnas i bakgrunden, utan att vara en aktiv del av lajvet.",
		        true,
		        false,
		        false);
		    
		    ?>	
        	<input type="radio" id="myslajvare_yes" name="NoIntrigue" value="1" onclick="handleRadioClick()" <?php if ($role->isMysLajvare()) echo 'checked="checked"'?>>
        	<label for="myslajvare_yes">Ja</label><br>
        	<input type="radio" id="myslajvare_no" name="NoIntrigue" value="0" onclick="handleRadioClick()"<?php if (!$role->isMysLajvare()) echo 'checked="checked"'?>>
        	<label for="myslajvare_no">Nej</label>
        	
    		<?php 
		  print_participant_question_end(true);
		}   
		
		
		
		if (LarperType::isInUse($current_larp)) {
		    print_participant_question_start(
		        "Vilken typ av lajvare är du?",
		        "Tänk igenom ditt val noga. Det är det här som i första hand kommer 
       			att avgöra hur mycket energi arrangörerna kommer lägga ner på dina intriger.     
       			Är du ny på lajv? Vi rekommenderar då att du att du väljer ett alternativ som ger mycket intriger. 
       			Erfarenhetsmässigt brukar man som ny lajvare ha mer nytta av mycket intriger än en 
       			erfaren lajvare som oftast har enklare hitta på egna infall under lajvet.",
		        $isPc,
		        true,
		        empty($role->LarperTypeId));
		    LarperType::selectionDropdown($current_larp, false, true, $role->LarperTypeId);
		    print_participant_question_end($isPc);
		    
		    print_participant_text_input(
		        "Kommentar till typ av lajvare",
		        "Exempel:<br>
             Jag är passiv lajvare, men kan tänka mig aktiva intriger som rör X, Y och Z.<br>
             Jag är karaktärslajvare och klarar mig utan intriger, men om ni har tankar om saker min karaktär kann vara involverad i som ger spel åt andra kan jag vara intresserad.<br>
             Jag är aktiv lajvare, men vill helst undvika våldsamma intriger.<br>
             Jag är action-lajvare och vill spela den sökta karaktären NN.<br>
             Jag är action-lajvare och har inget emot en våldsam död.",
		        "TypeOfLarperComment",
		        $role->TypeOfLarperComment,
		        "size='100' maxlength='200'",
		        false,
		        true);
        } 
        
		if (Ability::isInUse($current_larp)) {
		    $description = 'Specialförmågor är sådant som påverkar speltekniken. Exempelvis "tål ett extra slag", men inte "har bättre luktsinne". ';
		    if ($isPc) {
     		    $description .= "Om karaktären ska vara magi- eller alkemikunnig ska du innan din anmälan fått detta godkänt av magi-/alkemiarrangör, via e-post doh@berghemsvanner.se.
			Nyheter i regelsystemen för alkemi och magi kommer upp på hemsidan och facebook-sidan.";
     		}
     		
		    print_participant_question_start(
		        "Specialförmågor",
		        $description,
		        false,
		        true,
		        empty($role->getSelectedAbilityIds()));
		    selectionByArray('Ability' , Ability::allActive($current_larp), true, false, $role->getSelectedAbilityIds());
		    print_participant_question_end(false);
		    
		    print_participant_text_input(
		        "Om du valde specialstyrka/-förmåga, specificera och beskriv den/dessa så utförligt som möjligt",
		        "Motivera varför du har denna specialstyrka/-förmåga väl.",
		        "AbilityComment",
		        $role->AbilityComment,
		        "size='100' maxlength='200'",
		        false,
		        true);
        } 
        
        if (!$campaign->is_hfs()) {
            print_participant_text_input(
    		    "Var är karaktären född?",
    		    "Beskriv platsen om den inte är känd eller om den är relevant för rollen.",
    		    "Birthplace",
    		    $role->Birthplace,
    		    "size ='100' maxlength='100'",
                $isPc,
    		    true);
        }

		if (PlaceOfResidence::isInUse($current_larp)) {
		    print_participant_question_start(
		        "Var bor karaktären?",
		        "Tänk typ folkbokföringsadress, dvs även om karaktären tillfälligt är på platsen så vill vi veta var karaktären har sitt hem.",
		        $isPc,
		        true,
		        empty($role->PlaceOfResidenceId));
		    PlaceOfResidence::selectionDropdown($current_larp, false, $isPc, $role->PlaceOfResidenceId);
		    print_participant_question_end($isPc);
		}
		
		if (!$campaign->is_hfs()) {
    		print_participant_textarea(
    		    "Relationer med andra",
    		    "Tre karaktärer (på lajvet eller som bakgrundskaraktärer) som är viktiga för karaktären och mycket kort hur vi kan ge spel på dessa karaktärer.",
    		    "CharactersWithRelations",
    		    $role->CharactersWithRelations,
    		    "rows='4' maxlength='60000'",
    		    $isPc,
    		    true);
    
    		if ($isPc) {
    		    $headline = "Varför befinner sig karaktären på platsen?";
    		    $description = "Varför är karaktären på plats? Är hen bosatt och i så fall sedan hur länge? Har hen en anledning att besöka just nu? Brukar hen besöka platsen?";
    		}
    		else {
    		    $headline = "Om karaktären finns på plats, varför?";
    		    $description = "Om karaktären är på plats varför är karaktären det? Är hen bosatt och i så fall sedan hur länge? Har hen en anledning att besöka just nu? Brukar hen besöka platsen?<br>".
    		                     "Om karaktären inte är på plats, var är den då?";
    		}
    		print_participant_textarea(
    		    $headline,
    		    $description,
    		    "ReasonForBeingInSlowRiver",
    		    $role->ReasonForBeingInSlowRiver,
    		    "rows='4' maxlength='60000'",
    		    $isPc,
    		    true);
		}

		if (Religion::isInUse($current_larp)) {
		    print_participant_question_start(
		        "Vilken religion har karaktären?",
		        "Vissa religioner har bättre anseende än andra. Religionen kommer att påverka spel.",
		        $isPc,
		        true,
		        empty($role->ReligionId));
		    Religion::selectionDropdown($current_larp, false, $isPc, $role->ReligionId);
		    print_participant_question_end($isPc);
		    
		    print_participant_text_input(
		        "Religion förklaring",
		        "Skriv så noggrant du kan om karaktären inte har en 'vanlig' religion.",
		        "Religion",
		        $role->Religion,
		        "size='100' maxlength='200'",
		        false,
		        true);
		}
		 
		if (Belief::isInUse($current_larp)) {
		    print_participant_question_start(
		        "Hur troende är karaktären?",
		        "För vissa är religionen viktigare än andra. Hur är det för den här karaktären?",
		        $isPc,
		        true,
		        empty($role->BeliefId));
		    Belief::selectionDropdown($current_larp, false,$isPc, $role->BeliefId);
		    print_participant_question_end($isPc);
		} 
		
		if (Wealth::isInUse($current_larp)) {
		      $description = "";
		      if ($isPc) $description = "Om du är rik har du i regel ha någon form av affärer på gång. <br>
   			Det kommer att vara ett begränsat antal stenrika på lajvet och vi godkänner i regel inte nya. 
   			Undantag kan naturligtvis förekomma om det gynnar lajvet.   
   			Däremot är $campaign->Name en kampanj så det går att spela sig till att bli stenrik. 
   			Precis som det går att spela sig till rikedom går det att spela sig till att bli fattigare.";

		      print_participant_question_start(
		          "Hur rik är karaktären?",
		          $description,
		          $isPc,
		          true,
		          empty($role->WealthId));
		      Wealth::selectionDropdown($current_larp, false,$isPc, $role->WealthId);
		      print_participant_question_end($isPc);
		      
		}
		 
		if (RoleFunction::isInUse($current_larp)) {
		    print_participant_question_start(
		        "Har karaktären någon särskild funktion eller syssla?",
		        "",
		        false,
		        true,
		        empty($role->getSelectedRoleFunctionIds()));
		    selectionByArray('RoleFunction' , RoleFunction::allActive($current_larp), true, false, $role->getSelectedRoleFunctionIds()); 
		    print_participant_question_end(false);
		    
		    print_participant_text_input(
		        "Funktioner förklaring",
		        "Vill du förtydliga något kring funktioner eller lägga till något?",
		        "RoleFunctionComment",
		        $role->RoleFunctionComment,
		        "size='100' maxlength='200'",
		        false,
		        true);
		} 
		
		if (!$campaign->is_hfs()) {
    		print_participant_text_input(
    		    "Saker karaktären absolut inte vill spela på",
    		    "Är det något karaktären aldrig skulle göra?",
    		    "NotAcceptableIntrigues",
    		    $role->NotAcceptableIntrigues,
    		    "size='100' maxlength='200'",
    		    false,
    		    true);
    
    		
    		print_participant_textarea(
    		    "Karaktärens (mörka) baksida",
    		    "Har karaktären någon mörk hemlighet eller något personlighetsdrag som är till dennes nackdel? 
           	Har hen en hemlig skuld, ett hetsigt humör som sätter den i problem eller har hen kanske begått något brott? 
           	Det kan kännas svårt att göra karaktären sårbar på det här sättet, men försök. 
           	En mer nyanserad karaktär är ofta roligare och mer spännande att spela.",
    		    "DarkSecret",
    		    $role->DarkSecret,
    		    "rows='4' maxlength='60000'",
    		    $isPc,
    		    true);
    
    		print_participant_text_input(
    		    "Karaktärens (mörka) baksida - intrigidéer",
    		    "Hur kan vi spela på karaktärens mörka baksida?",
    		    "DarkSecretIntrigueIdeas",
    		    $role->DarkSecretIntrigueIdeas,
    		    "size='100' maxlength='200'",
    		    $isPc,
    		    true);
    
		  }
		  if ($campaign->is_hfs() && ($current_person->getAgeAtLarp($current_larp) < 15 || $admin)) {
		      if ($admin) echo "<div class='itemcontainer' style='color:blue'><b>Kommentar om formuläret:</b><br><i>OBS! Frågorna om suprhjätenamn och superkrafter visas bara om man är under 15 år</i></div>";
		      print_participant_text_input(
		          "Superhjältenamn",
		          "Tex: <i>Eldstorm</i> eller <i>Nattfalken</i><br> 
                    Du kan INTE ta superhjältenamn som redan finns, tex <i>Spindel-mannen</i> eller <i>Röda masken</i>",
		          "SuperHeroName",
		          $role->SuperHeroName,
		          "size='100' maxlength='200'",
		          $isPc,
		          true);
		      
		      if (SuperPowerActive::isInUse($current_larp)) {
		          print_participant_question_start(
		              "Superkraft Aktiv",
		              "Välj 1 superkraft. Din karaktär kommer bara kunna använda den en gång under lajvet, så tänk efter när den kan komma till nytta. ",
		              false,
		              true,
		              false);
		          selectionByArray('SuperPowerActive' , SuperPowerActive::allActive($current_larp), false, true, $role->getSelectedActiveSuperPowerIds());
		          print_participant_question_end(false);
		          
		      }
		      
		      if (SuperPowerPassive::isInUse($current_larp)) {
		          print_participant_question_start(
		              "Superkraft Passiv",
		              "Välj upp till tre passiva superkrafter. En passiv kraft är en kraft du inte kan använda. Den är stoppad av maskinen.",
		              false,
		              true,
		              false);
		          selectionByArray('SuperPowerPassive' , SuperPowerPassive::allActive($current_larp), true, false, $role->getSelectedPassiveSuperPowerIds());
		          print_participant_question_end(false);
		          
		      }
		      
		  }

		  print_participant_textarea(
		      "Övrig information",
		      "Är det något annat kring karaktären arrangörerna bör veta om karaktären?",
		      "OtherInformation",
		      $role->OtherInformation,
		      "rows='4' maxlength='60000'",
		      false,
		      true);
		  
		  
			if ($admin) {
			    //Om bara tittar på formuläret som arrangör får man inte lyckas skicka in
			    $type = "button";
			} else {
			    $type = "submit";
		    }
		    
		    ?>


			<div class='center'>
			<input type='<?php echo $type ?>' name='SaveButton' class='button-18' value='<?php echo default_value('action') ?>'>
			<?php 
			if (!$isPc || $role->isRegistered($current_larp)) {
			    echo "<button type='$type' name='SaveAndLockButton' value='1' class='button-18'><i class='fa-solid fa-share'></i> ";
			    default_value('action');
			    echo " och skicka in för godkännande</button>";
			}
			?>
			</div>
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