<?php
include_once 'header.php';
include 'navigation.php';

$campaign = $current_larp->getCampaign();
$currency = $campaign->Currency;
$larps_in_campaign=LARP::getPreviousLarpsInCampaign($current_larp);
?>


    <div class="content">
        <h1>Sätt pengar på många karaktärer</h1>
         <p>Förklaring: Pengarna för en karaktär beräknas som summan av alla delar. <br> 
         Om en karaktär har både rikedoms nivå och varit med på ett tidigare lajv så kommer den att få för rikedomsnivå + för tidigare lajv + den fasta summan.<br>
         Om summan blir 0 sätts den inte.</p>
         <p>Exempel: Man vill ge alla karaktärer lika mycket pengar som de hade vid slutet av förra lajvet, 
         och att nya karaktärer får utifrån rikedomsnivå de har.<br><br>
         Då får man köra den här sättningen två gånger: <br>
         * Första gången väljer man "Alla" och 100% från förra lajvet.<br>
         * Andra gången väljer man "Alla de som inte har något värde satt" och så ställer man in pengar på 
         rikedomsnivåerna.<br>
         * Om man har karaktärer som saknar rikedomsnivå kan man behöva köra den en tredje gång.<br> 
         Då väljer man "Alla de som inte har något värde satt" och sätter en lämplig summa i rutan för "Fast summa".</p>
         
        <form action="logic/role_money_setup_save.php" method="post" >
        <h2>Vilka karaktärer ska få pengar</h2>
			<div class="question">
				<label for="which_roles">Vilken typ av karaktär</label>&nbsp;<font style="color:red">*</font>
				<br> 
				<input type="radio" id="which_roles_all" name="which_roles" value="all" checked='checked' required>
				<label for="which_roles_all">Alla</label><br>
				<input type="radio" id="which_roles_main" name="which_roles" value="main" required>
				<label for="which_roles_main">Huvudkaraktärer</label><br>
				<input type="radio" id="which_roles_notmain" name="which_roles" value="notmain" required>
				<label for="which_roles_notmain">Övriga karaktärer</label><br>
			</div>

			<div class="question">
				<label for="which_roles_effect">Vilka ska påverkas</label>&nbsp;<font style="color:red">*</font>
				<br> 
				De som inte påverkas ändrar vi inte pengarna på.<br>
				<input type="radio" id="which_roles_effect_all" name="which_roles_effect" value="all" required>
				<label for="which_roles_effect_all">Alla</label><br>
				<input type="radio" id="which_roles_effect_notset" name="which_roles_effect" value="notset" checked='checked' required>
				<label for="which_roles_effect_notset">De som inte har något värde satt</label><br>
			</div>
        	
        <?php if (Wealth::isInUse($current_larp)) { ?>
            <h2>Pengar utifrån rikedomsnivå</h2>
            <p>Så här mycket pengar ska en karaktär få utifrån hur rik den är.<br>Sätt 0 på pengar per rikedomsnivå om du inte vill att rikedomsnivå ska påverka hur mycket pengar de får.</p>
            	<?php 
            	$wealths = Wealth::allActive($current_larp);
            	foreach ($wealths as $wealth) {
            	    echo "<div class='question'>";
            	    echo "<label for='wealth_$wealth->Id'>$wealth->Name</label>&nbsp;<font style='color:red'>*</font>";
            	    echo "<br>";
            	    echo "<input type='number' id='wealth_$wealth->Id' name='wealth_$wealth->Id' value='0' min='0' required> $currency";
            	    echo "</div>";
            	}
            }
        	
        	?>
        <h2>Pengar från tidigare lajv</h2>
        <p>Hur stor del av vad karaktären hade i slutet på tidigare lajv ska den få på det här lajvet.<br>
        Om du sätter olika värden på min och max % så kommer procenten att slumpas mellan dem för varje karaktär.<br>
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
	     Karaktärer som inte var med på lajvet ska ha summa utifrån rikedomsnivå:
            	<?php 
            	$wealths = Wealth::allActive($current_larp);
            	foreach ($wealths as $wealth) {
            	    echo "<div class='question'>";
            	    echo "<label for='new_wealth_$wealth->Id'>$wealth->Name</label>&nbsp;<font style='color:red'>*</font>";
            	    echo "<br>";
            	    echo "<input type='number' id='new_wealth_$wealth->Id' name='new_wealth_$wealth->Id' value='0' min='0' required> $currency";
            	    echo "</div>";
            	}

        	
        	?>
			
			
        <h2>Fast summa</h2>
        <p>Hur mycket extra pengar ska varje karaktär få.<br>
        Om du sätter olika värden på min och max så kommer summan att slumpas mellan dem för varje karaktär.<br>
        Sätt summan till 0 om du inte vill lägga till en fast summa till alla karaktärer.</p>
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