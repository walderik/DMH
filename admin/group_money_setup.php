<?php
include_once 'header.php';
include 'navigation.php';

$campaign = $current_larp->getCampaign();
$currency = $campaign->Currency;
$larps_in_campaign=LARP::getPreviousLarpsInCampaign($current_larp);
?>


    <div class="content">
        <h1>Sätt pengar på många grupper</h1>
         <p>Förklaring: Pengarna för en grupp beräknas som summan av alla delar. <br> 
         Om en grupp har både rikedoms nivå och varit med på ett tidigare lajv så kommer den att få för rikedomsnivå + för tidigare lajv + den fasta summan.<br>
         Om summan blir 0 sätts den inte.</p>
         <p>Exempel: Man vill ge alla grupper lika mycket pengar som de hade vid slutet av förra lajvet, 
         och att nya grupper får utifrån rikedomsnivå de har.<br><br>
         Då får man köra den här sättningen två gånger: <br>
         * Första gången väljer man "Alla" och 100% från förra lajvet.<br>
         * Andra gången väljer man "Alla de som inte har något värde satt" och så ställer man in pengar på 
         rikedomsnivåerna.
         </p>
         
        <form action="logic/group_money_setup_save.php" method="post" >
        <h2>Vilka grupper ska få pengar</h2>

			<div class="question">
				<label for="which_groups_effect">Vilka ska påverkas</label>&nbsp;<font style="color:red">*</font>
				<br> 
				De som inte påverkas ändrar vi inte pengarna på.<br>
				<input type="radio" id="which_groups_effect_all" name="which_groups_effect" value="all" required>
				<label for="which_groups_effect_all">Alla</label><br>
				<input type="radio" id="which_groups_effect_notset" name="which_groups_effect" value="notset" checked='checked' required>
				<label for="which_groups_effect_notset">De som inte har något värde satt</label><br>
			</div>
        	
        <h2>Pengar utifrån rikedomsnivå</h2>
        <p>Så här mycket pengar ska en grupp få utifrån hur rik den är.<br>Sätt 0 på pengar per rikedomsnivå om du inte vill att rikedomsnivå ska påverka hur mycket pengar de får.</p>
        	<?php 
        	$wealths = Wealth::allActive($current_larp);
        	foreach ($wealths as $wealth) {
        	    
        	    echo "<div class='question'>";
        	    echo "<label for='wealth_$wealth->Id'>$wealth->Name</label>&nbsp;<font style='color:red'>*</font>";
        	    //echo "<br>";
        	    echo "<input type='number' id='wealth_$wealth->Id' name='wealth_$wealth->Id' value='0' min='0' required> $currency";
                echo ", ";
        	    echo "<label for='wealth_".$wealth->Id."_per_member'>per deltagare i gruppen</label>&nbsp;<font style='color:red'>*</font>";
        	    //echo "<br>";
        	    echo "<input type='number' id='wealth_".$wealth->Id."_per_member' name='wealth_".$wealth->Id."_per_member' value='0' min='0' required> $currency";
        	    
        	    
        	    
        	    echo "</div>";
        	}
        	
        	
        	?>
        <h2>Pengar från tidigare lajv</h2>
        <p>Hur stor del av vad gruppen hade i slutet på tidigare lajv ska den få på det här lajvet.<br>
        Om du sätter olika värden på min och max % så kommer procenten att slumpas mellan dem för varje grupp.<br>
        Sätt båda procent till 0 om du inte vill att något tidigare lajv ska påverka pengarna på det här lajvet.</p>
			<div class="question">
				<label for="larp">Välj lajv</label></font>

			     <select name='larp' id='larp'>
    			     <?php
    			     foreach ($larps_in_campaign as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     ?>
			     </select>

			</div>
        
			<div class="question">
				<label for="percent_min">Min % av resultatet</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="number" id="percent_min" name="percent_min" value="0" min="0" required>
			</div>
			<div class="question">
				<label for="percent_max">Max % av resultatet</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="number" id="percent_max" name="percent_max" value="0" min="0" required>
			</div>
			
        <h2>Fast summa</h2>
        <p>Hur mycket extra pengar ska varje grupp få.<br>
        Om du sätter olika värden på min och max så kommer summan att slumpas mellan dem för varje grupp.<br>
        Sätt summan till 0 om du inte vill lägga till en fast summa till alla grupper.</p>
			<div class="question">
				<label for="fixed_sum_min">Fast summa min</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="number" id="fixed_sum_min" name="fixed_sum_min" value="0" min="0" required> <?php echo $currency ?>
			</div>
			<div class="question">
				<label for="fixed_sum_max">Fast summa max</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="number" id="fixed_sum_max" name="fixed_sum_max" value="0" min="0" required> <?php echo $currency ?>
			</div>
			
			
			
			
			
        	<input type="submit" value="Sätt">
        </form>
            </div>
	
</body>

</html>