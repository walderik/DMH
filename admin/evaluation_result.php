<?php
include_once 'header.php';

include 'navigation.php';


function chart($headline, $id, ?bool $include_dont_know=false, ?String $explanation="") {
    global $current_larp;
    $question_result = EvaluationNumberQuestion::get($id, $current_larp);
    $val = $question_result->valuesArr;
    echo "<div class='chart'>\n";
    echo "$headline\n";
    
    echo "<canvas id='$id' height='100em'></canvas>\n";
    echo "</div>\n";
    
    echo "<script>";
    echo "const chart_$id = document.getElementById('$id');";
    
    
    echo "new Chart(chart_$id, {
        type: 'bar',
        data: {";
    if ($include_dont_know) echo "labels: ['Vet inte', '1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],";
    else echo "labels: ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10'],";
    echo "datasets: [{
                label: 'Antal',";
    if ($include_dont_know) echo "data: [$val[0], $val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10]],";
    else echo "data: [$val[1], $val[2], $val[3], $val[4], $val[5], $val[6], $val[7], $val[8], $val[9], $val[10]],";
    
                
    echo "            borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });";
    echo "</script>";
          
}


function comments($headline, $id) {
    global $current_larp;
    $question_result = EvaluationCommentsQuestion::get($id, $current_larp);
    
    echo "<div class='comments'>\n";
    echo "$headline\n";
    
    foreach($question_result->comments as $comment) {
        echo "<p>$comment</p>\n";
    }
    echo "</div>\n";
    
}


$campaign = $current_larp->getCampaign();

?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="content">

	<h1>Resultat av utvärdering</h1>
	<?php 
	$question_result = EvaluationNumberQuestion::get("larp_q1", $current_larp);
	echo "<p>$question_result->number_of_responders utvärderingar har lämnats in. Om det är mindre än 5 st visas inget.</p>";
	
	if ($question_result->number_of_responders < 5) exit;
	
	
	?>
	
    <h2>Betygsätt lajvet</h2>
	
		<?php chart("Arrangörerna (professionalism, bemötande, nåbarahet m.m)","larp_q1")?>
		<?php chart("Hemsidan (information, navigering, lättläst m.m)","larp_q2")?>
		<?php chart("Prissättning (1 = för högt pris)","larp_q3")?>
		<?php chart("Intrigerna","larp_q4")?>
		<?php chart("Logistik (transporter, parkering m.m.)","larp_q5")?>
		<?php chart("Bekvämligheter (mat, dass, vatten, ved m.m)","larp_q6")?>
		<?php chart("Området","larp_q7")?>
		<?php chart("Betygsätt din upplevelse/-er under lajvet","larp_q8")?>
		<?php chart("Lajvets helhetsbetyg","larp_q9")?>
		<?php comments("Övrigt/kommentarer", "larp_comment")?>

		<h2>Hur väl stämmer följande påståenden överens med din upplevelse av <?php echo $current_larp->Name ?></h2>
		<?php chart("Det var ett välorganiserat lajv","exp_q1", true)?>
		<?php chart("Det var ett nybörjarvänligt lajv","exp_q2", true)?>
		<?php //chart("Det var ett nybörjarvänligt lajv","exp_q3", true)?>
		<?php chart("Det var ett lajv för erfarna","exp_q4", true)?>
		<?php chart("Det var ett barn- och familjevänligt lajv","exp_q5", true)?>
		<?php chart("Jag hade roligt på lajvet","exp_q6", true)?>
		<?php chart("Jag tänker åka på fler ".$current_larp->getCampaign()->Name."-lajv om det blir några fler","exp_q7", true)?>
		<?php comments("Övrigt/kommentarer", "exp_comment")?>

		<h2>Information</h2>
		<?php chart("Det var en lättnavigerad hemsida","info_q1")?>
		<?php chart("Det fanns tillräckligt med information på hemsidan","info_q2")?>
		<?php chart("Uppskatta hur mycket av informationen på hemsidan som du har läst","info_q3", false, "Siffran 1 = 10% (nästan inget) och siffran 10 = 100% (allt)")?>
		<?php chart("Uppskatta hur mycket av informationen av utskicket som du har läst","info_q4", false, "Siffran 1 = 10% (nästan inget) och siffran 10 = 100% (allt)")?>
		<?php comments("Vad bör vi utveckla på hemsidan till nästa gång?", "info_dev")?>
		<?php comments("Övrigt/kommentarer", "info_comment")?>

		<h2>Maten</h2>
		<?php chart("Den förbeställda maten var god","food_q1",true)?>
		<?php chart("Den förbeställda maten var prisvärd","food_q2",true)?>
		<?php comments("Övrigt/kommentarer", "food_comment")?>

		<h2>Regler</h2>
		<?php chart("Stridssystemet var enkelt att förstå och spela på","rules_q1")?>
		<?php if ($campaign->is_dmh()) chart("Det var INTE ett problem att det var tillåtet med alkohol på lajvet","rules_q2") ?>
		<?php chart("Reglerna kring rökning var bra","rules_q3")?>
		<?php comments("Övrigt/kommentarer", "rules_comment")?>

		<h2>In-lajv valutan</h2>
		<?php chart("Valutasystemet var bra","currency_q1")?>
		<?php chart("Det var lätt att förstå vad man skulle ta betalt för en tjänst","currency_q2")?>
		<?php comments("Övrigt/kommentarer", "currency_comment")?>


		<h2>Bemötande</h2>
		<?php chart("Arrangörerna var trevliga och hjälpsamma","org_q1")?>
		<?php chart("Arrangörerna var professionella","org_q2")?>
		<?php chart("Arrangörerna var lätta att få kontakt med innan lajvet","org_q3")?>
		<?php chart("Arrangörerna var lätta att få kontakt med under lajvet","org_q4")?>
		<?php comments("Övrigt/kommentarer", "org_comment")?>


		<h2>Speltekniska system</h2>
		<?php chart("Det var ett bra system att man kunde gå till telegrafen för att få hjälp med intrigerna","game_dmh_q1")?>
		<?php chart("Handelssystemet med resurskort och verksamheter var ett bra system","game_dmh_q2")?>
		<?php chart("Tjuvsystemet med föremål märkta med grönt band som man fick stjäla var ett roligt inslag på lajvet","game_dmh_q3")?>
		<?php comments("Övrigt/kommentarer", "game_comment")?>
	

		<h2>Avslutande</h2>
		<?php comments("Vad tycker du varit positivt med lajvet?", "finish_positive")?>
		<?php comments("Vad tycker du varit negativt med lajvet?", "finish_negative")?>
		<?php comments("Vad tycker du att vi ska utveckla till nästa gång?", "finish_develop")?>
		<?php comments("Övrigt/kommentarer", "finish_comment")?>
	
	
	</div>
