<?php
require 'header.php';

if (!$current_larp->isEnded()) {
    header('Location: index.php');
    exit;
}

$viewOnly = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['PersonId'])) {
        $PersonId = $_POST['PersonId'];
    }
    if (isset($_POST['ViewOnly'])) {
        $viewOnly = true;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['PersonId'])) {
        $PersonId = $_GET['PersonId'];
    }
    if (isset($_GET['ViewOnly'])) {
        $viewOnly = true;
    }
}


$person = Person::newWithDefault();

if (isset($PersonId)) {
    $person = Person::loadById($PersonId);
} elseif(!$viewOnly) {
    header('Location: index.php');
    exit;
}

if (!$viewOnly && $person->UserId != $current_user->Id) {
    header('Location: index.php');
    exit;
}


function slider($headline, $id, ?String $explanation="") {
    global $viewOnly;
    
    $min = 0;
    $max = 10;
    $value = 0;
    echo "<div class='question'>\n";
    echo "<label for='$id'>$headline</label>\n";
    
    if (!empty($explanation)) echo "<div class='explanation'>$explanation</div>\n";
    
    if (!$viewOnly) {
        echo "<div class='slidecontainer'>\n";
        $value_text = $value;
        if ($value == 0) $value_text = "Vet inte/inte aktuellt";
        echo "<input type='range' min='$min' max='$max' value='$value' class='slider' id='$id' name='$id' oninput='slider_value(\"$id\")'> &nbsp; &nbsp;Värde: <span id='$id"."_val'>$value_text</span>\n";
        echo "</div>\n";
    }
    echo "</div>\n";
    
}

function textQuestion($headline, $id) {
    global $viewOnly;
    
    echo "<div class='question'>\n";
    echo "<label for='$id'>$headline</label><br>\n";
    echo "<textarea id='$id' name='$id' rows='4' cols='100' maxlength='2000'";
    if ($viewOnly) echo " disabled=disabled ";
    echo "></textarea>\n";
    echo "</div>\n";
    
}

$campaign = $current_larp->getCampaign();

include 'navigation.php';

?>

<script>

// Update the current slider value (each time you drag the slider handle)
function slider_value(sliderId) {
	slider = document.getElementById(sliderId);
	output = document.getElementById(sliderId+"_val");

	let value = slider.value;
	let value_text = value;
    if (value == 0) value_text = "Vet inte/inte aktuellt";

	
  	output.innerHTML = value_text;
}



</script>


<style>
body {
  counter-reset: question;
}

label::before {
  counter-increment: question;
  content: counter(question) ". ";
}
</style>

	<div class="content">
		<h1>Utvärdering av <span style='color:red'><?php echo $current_larp->Name; ?></span>  för <?php echo $person->Name; ?></h1>
			<p>Kontrollera att du lämnar in utvärdering för rätt lajv.<br><br>Vi sparar inte vilka svar du har angett, bara att du har lämnat en utvärdering. Utvärderingen sparas anonymt.</p>
			<form action="logic/evaluation_save.php" method="post">
			<input type="hidden" id="Id" name="Id" value="<?php if (!$viewOnly) echo $person->getRegistration($current_larp)->Id ?>">
			<input type="hidden" id="Age" name="Age" value="<?php if (!$viewOnly) echo $person->getAgeAtLarp($current_larp)?> ">

            
            <h2>Betygsätt lajvet: skala 1-10</h2>
            
    			<div class="explanation">
				1: lägsta, sämst<br>
				10: högsta, bäst
				</div>
				
			<?php slider("Arrangörerna (professionalism, bemötande, nåbarahet m.m)","larp_q1")?>
			<?php slider("Hemsidan (information, navigering, lättläst m.m)","larp_q2")?>
			<?php slider("Prissättning (1 = för högt pris)","larp_q3")?>
			<?php slider("Intrigerna","larp_q4")?>
			<?php slider("Logistik (transporter, parkering m.m.)","larp_q5")?>
			<?php slider("Bekvämligheter (mat, dass, vatten, ved m.m)","larp_q6")?>
			<?php slider("Området","larp_q7")?>
			<?php slider("Betygsätt din upplevelse/-er under lajvet","larp_q8")?>
			<?php slider("Lajvets helhetsbetyg","larp_q9")?>
			
			<?php textQuestion("Övrigt/kommentarer", "larp_comment")?>

		<h2>Hur väl stämmer följande påståenden överens med din upplevelse av <?php echo $current_larp->Name ?></h2>
			<div class="explanation">
			Om du inte vet eller inte kan svara på frågan lämna värdet på "Vet inte", (0).<br>
			1: stämmer inte alls<br>
			10: stämmer mycket väl överens
			</div>
			<?php slider("Det var ett välorganiserat lajv","exp_q1")?>
			<?php slider("Det var ett nybörjarvänligt lajv","exp_q2")?>
			<?php //slider("Det var ett nybörjarvänligt lajv","exp_q3")?>
			<?php slider("Det var ett lajv för erfarna","exp_q4")?>
			<?php slider("Det var ett barn- och familjevänligt lajv","exp_q5")?>
			<?php slider("Jag hade roligt på lajvet","exp_q6")?>
			<?php slider("Jag tänker åka på fler ".$current_larp->getCampaign()->Name."-lajv om det blir några fler","exp_q7")?>

			<?php textQuestion("Övrigt/kommentarer", "exp_comment")?>

		<h2>Information</h2>
			<?php slider("Det var en lättnavigerad hemsida","info_q1")?>
			<?php slider("Det fanns tillräckligt med information på hemsidan","info_q2")?>
			<?php slider("Uppskatta hur mycket av informationen på hemsidan som du har läst","info_q3", "Siffran 1 = 10% (nästan inget) och siffran 10 = 100% (allt)")?>
			<?php slider("Uppskatta hur mycket av informationen av utskicket som du har läst","info_q4","Siffran 1 = 10% (nästan inget) och siffran 10 = 100% (allt)")?>

			<?php textQuestion("Vad bör vi utveckla på hemsidan till nästa gång?", "info_dev")?>
			<?php textQuestion("Övrigt/kommentarer", "info_comment")?>

		<h2>Maten</h2>
			<?php slider("Den förbeställda maten var god","food_q1")?>
			<?php slider("Den förbeställda maten var prisvärd","food_q2")?>
			<?php textQuestion("Övrigt/kommentarer", "food_comment")?>

		<h2>Regler</h2>
			<?php slider("Stridssystemet var enkelt att förstå och spela på","rules_q1")?>
			<?php slider("Reglerna kring alkohol var bra","rules_q2"); ?>
			<?php slider("Reglerna kring rökning var bra","rules_q3")?>
			<?php textQuestion("Övrigt/kommentarer", "rules_comment")?>

		<h2>In-lajv valutan</h2>
			<?php slider("Valutasystemet var bra","currency_q1")?>
			<?php slider("Det var lätt att förstå vad man skulle ta betalt för en tjänst","currency_q2")?>
			<?php textQuestion("Övrigt/kommentarer", "currency_comment")?>

		<h2>Bemötande</h2>
			<?php slider("Arrangörerna var trevliga och hjälpsamma","org_q1")?>
			<?php slider("Arrangörerna var professionella","org_q2")?>
			<?php slider("Arrangörerna var lätta att få kontakt med innan lajvet","org_q3")?>
			<?php slider("Arrangörerna var lätta att få kontakt med under lajvet","org_q4")?>
			<?php textQuestion("Övrigt/kommentarer", "org_comment")?>


		<h2>Trygghet / sjukvård</h2>
			<?php slider("Trygghetsvärdarna var lättillgängliga","health_q1")?>
			<?php slider("Jag kände mig trygg på arrangemanget","health_q2")?>
			<?php slider("Sjukvården fungerade bra","health_q3")?>
			<?php textQuestion("Övrigt/kommentarer", "health_comment")?>
		
		<?php if ($campaign->is_kir()) { 
		} else {?>

		<h2>Speltekniska system</h2>
			<?php if ($campaign->is_dmh()) { ?>
    			<?php slider("Det var ett bra system att man kunde gå till telegrafen för att få hjälp med intrigerna","game_q1")?>
    			<?php slider("Handelssystemet med resurskort och verksamheter var ett bra system","game_q2")?>
    			<?php slider("Tjuvsystemet med föremål märkta med grönt band som man fick stjäla var ett roligt inslag på lajvet","game_q3")?>
			<?php } elseif ($campaign->is_doh()) { ?>
				<?php slider("Magisystemet fungerade bra","game_q1")?>
				<?php slider("Alkemisystemet fungerade bra","game_q2")?>
				<?php slider("Synerna fungerade bra","game_q3")?>
				<?php slider("Handelssystemet fungerade bra","game_q4")?>
				<?php slider("Barnaktiviteterna var bra","game_q5")?>
				<?php slider("Jag som förälder kände mig trygg med barnaktiviteterna","game_q6")?>
				<?php slider("Barntältet var bra","game_q7")?>
				<?php slider("Det var bra att starta lajvet med en kicker","game_q8")?>
				<?php slider("Det var bra att ha ett sekretariat/bank att vända sig till","game_q9")?>
			
			<?php } elseif ($campaign->is_me()) { ?>
			
			<?php } ?>

			<?php textQuestion("Övrigt/kommentarer", "game_comment")?>
		<?php } ?>

		<h2>Avslutande</h2>
			<?php textQuestion("Vad tycker du varit positivt med lajvet?", "finish_positive")?>
			<?php textQuestion("Vad tycker du varit negativt med lajvet?", "finish_negative")?>
			<?php textQuestion("Vad tycker du att vi ska utveckla till nästa gång?", "finish_develop")?>
			<?php textQuestion("Övrigt/kommentarer", "finish_comment")?>
            
            <?php if (!$viewOnly) {?>
            <input type="submit" value="Skicka in">
            <?php }?>
            </form>

<p>Frågorna kommer från <a href="https://morgondagensgryning.se/" target="_blank">Morgondagens Gryning</a>. Åk gärna på deras lajv också.</p>
