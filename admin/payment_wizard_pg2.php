<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_date=$_POST['first_date'];
    $last_date=$_POST['last_date'];
    $number_of_time_intervals=$_POST['number_of_time_intervals'];
    $min_age=$_POST['min_age'];
    $max_age=$_POST['max_age'];
    $number_of_age_groups=$_POST['number_of_age_groups'];
    $number_of_food_options=$_POST['number_of_food_options'];
} else {
    header('Location: payment_information_admin.php');
    exit;
}
    

include 'navigation.php';

?>


<script>
function updateFromAge(num) {
    let from = document.getElementById("age["+num+"]");
    let to = document.getElementById("startage" + (num+1));
 
  	to.innerHTML = parseInt(from.value) + 1;

} 
function updateFromDate(num) {
    let from = document.getElementById("date["+num+"]");
    let to = document.getElementById("startdate" + (num+1));


    var result = new Date(from.value);
    result.setDate(result.getDate() + 1);
  
  	to.innerHTML = result.toLocaleDateString();

} 

</script>

    <div class="content">
        <h1>Uppsättning av deltagaravgifter - sida 2 av 3</h1>
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
        
         
        <form action="payment_wizard_pg3.php" method="post" >
			<input type="hidden" id="first_date" name="first_date" value="<?php echo $first_date; ?>">
			<input type="hidden" id="last_date" name="last_date" value="<?php echo $last_date; ?>">
			<input type="hidden" id="number_of_time_intervals" name="number_of_time_intervals" value="<?php echo $number_of_time_intervals; ?>">
			<input type="hidden" id="min_age" name="min_age" value="<?php echo $min_age; ?>">
			<input type="hidden" id="max_age" name="max_age" value="<?php echo $max_age; ?>">
			<input type="hidden" id="number_of_age_groups" name="number_of_age_groups" value="<?php echo $number_of_age_groups; ?>">
			<input type="hidden" id="number_of_food_options" name="number_of_food_options" value="<?php echo $number_of_food_options; ?>">

        
        <h2>Datumintervaller</h2>
        <table class="small_data">
        	<tr><th>Från</th><th>Till</th></tr>
        <?php 
        for ($i = 0; $i < $number_of_time_intervals; ++$i) {
                echo "<tr>";
                if ($i == 0) {
                    //Första raden
                    echo "<td>$first_date</td>";
                }
                else {
                    echo "<td><div id='startdate$i'></div></td>";
                }
                
                if ($i+1 == $number_of_time_intervals) {
                    //Sista raden
                    echo "<td>$last_date</td>";
                }
                else {
                    echo "<td><input type='date' id='date[$i]' name='date[$i]' min='$first_date' max='$last_date' onBlur='updateFromDate($i)' required></td>";
                }
                

            }
            
        ?>
        </table>
        
                <h2>Åldersintervaller</h2>
        <table class="small_data">
        	<tr><th>Från</th><th>Till</th></tr>
        <?php 
        for ($i = 0; $i < $number_of_age_groups; ++$i) {
                echo "<tr>";
                if ($i == 0) {
                    //Första raden
                    echo "<td>$min_age</td>";
                }
                else {
                    echo "<td><div id='startage$i'></div></td>";
                }
                
                if ($i+1 == $number_of_age_groups) {
                    //Sista raden
                    echo "<td>$max_age</td>";
                }
                else {
                    echo "<td><input type='number' id='age[$i]' name='age[$i]' min='$min_age' max='$max_age' onBlur='updateFromAge($i)' required></td>";
                }
                

            }
            
        ?>
        </table>
        <br>
        
        <?php if ($number_of_food_options > 0) {?>
        <h2>Matalternativ</h2>
        <p>Beskrivningen kommer att visas för deltagarna vid anmälan.</p>
        <table class="small_data">
    	<tr><th>Beskrivning</th></tr>
        <?php 
        for ($i = 0; $i < $number_of_food_options; ++$i) {
            echo "<tr>";
            echo "<td><input type='text' id='food_description[$i]' name='food_description[$i]' max-length='50' required></td>";
            echo "</tr>";
        }
            
        ?>
        </table>
 
        
		<br>   
		<?php } ?>     
        	<input type="submit" value="Nästa">
        </form>
            </div>
	
</body>

</html>