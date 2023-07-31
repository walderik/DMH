<?php
include_once 'header.php';
include 'navigation.php';
?>


    <div class="content">
        <h1>Uppsättning av deltagaravgifter - sida 1 av 3</h1>
        <p>Den här guiden kommer att hjälpa dig att enkelt sätta upp 
        deltagaravgifterna för lajvet så att de täcker in alla relavanta datum och åldrar.<br><br>
        <?php 
        $payment_array = PaymentInformation::allBySelectedLARP($current_larp);
        if (!empty($payment_array)) {           
        ?>
        <strong>OBS!</strong> Alla tidigare inställningar för avgifter kommer att raderas. <br>
        <?php 
        }?>
        <?php 
        if (count(Registration::allBySelectedLARP($current_larp)) > 0) {
        ?>
        
        <strong>OBS!</strong> Avgiften för de anmälningar som redan är gjorda kommer inte att påverkas.<br>
         <?php 
        }?>
        
         
        <form action="payment_wizard_pg2.php" method="post" >
        <h2>Datum</h2>
			<div class="question">
				<label for="first_date">Första datum</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="date" id="first_date" name="first_date" value="<?php echo date("Y-m-d"); ?>" required>
			</div>
			<div class="question">
				<label for="last_date">Sista datum</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="date" id="last_date" name="last_date" value="<?php echo $current_larp->LatestRegistrationDate; ?>" required>
			</div>
			<div class="question">
				<label for="number_of_time_intervals">Antal tidsintervaller</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="number" id="number_of_time_intervals" name="number_of_time_intervals" min="1" value="1" required>
			</div>
        
        <h2>Åldrar</h2>
        <p>Tänk på att ha med alla åldrar som kan delta. Även om kostnaden är 0 för dem.</p>
			<div class="question">
				<label for="min_age">Minsta ålder</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="text" id="min_age" name="min_age" value="<?php echo $current_larp->getCampaign()->MinimumAge; ?>" size="10" required>
			</div>
			<div class="question">
				<label for="max_age">Högsta ålder</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="text" id="max_age" name="max_age" value="200" size="10"required>
			</div>
 			<div class="question">
				<label for="number_of_age_groups">Antal åldersgrupper</label>&nbsp;<font style="color:red">*</font>
				<br> <input type="text" id="number_of_age_groups" name="number_of_age_groups" value="1" size="10"required>
			</div>
        
        	<input type="submit" value="Nästa">
        </form>
            </div>
	
</body>

</html>