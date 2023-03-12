<?php
include_once 'header_subpage.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_date=$_POST['first_date'];
    $last_date=$_POST['last_date'];
    $number_of_time_intervals=$_POST['number_of_time_intervals'];
    $min_age=$_POST['min_age'];
    $max_age=$_POST['max_age'];
    $number_of_age_groups=$_POST['number_of_age_groups'];
    
} else {
    header('Location: payment_information_admin.php');
    exit;
}
    



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
        <h1>Uppsättning av deltagaravgifter - sida 2 av NN</h1>
        <p>Den här guiden kommer att hjälpa dig att enkelt sätta upp 
        deltagaravgifterna för lajvet så att de täcker in alla relavanta datum och åldrar.<br><br>
        <?php 
        $payment_array = PaymentInformation::allBySelectedLARP();
        if (!empty($payment_array)) {           
        ?>
        <strong>OBS!</strong> Alla tidigare inställningar för avgifter kommer att raderas. <br>
        <?php 
        }?>
        <?php 
        if (count(Registration::allBySelectedLARP()) > 0) {
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
        
        	<input type="submit" value="Nästa">
        </form>
            </div>
	
</body>

</html>