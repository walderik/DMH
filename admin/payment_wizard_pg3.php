<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_date=$_POST['first_date'];
    $last_date=$_POST['last_date'];
    $number_of_time_intervals=$_POST['number_of_time_intervals'];
    $min_age=$_POST['min_age'];
    $max_age=$_POST['max_age'];
    $number_of_age_groups=$_POST['number_of_age_groups'];
    
    if (isset($_POST['date'])) $dateArr = $_POST['date'];
    if (isset($_POST['age'])) $ageArr = $_POST['age'];
    
} else {
    header('Location: payment_information_admin.php');
    exit;
}
    

include 'navigation_subpage.php';

?>




    <div class="content">
        <h1>Uppsättning av deltagaravgifter - sida 3 av 3</h1>
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
         
        <form action="logic/payment_wizard_save.php" method="post" >
			<input type="hidden" id="first_date" name="first_date" value="<?php echo $first_date; ?>">
			<input type="hidden" id="last_date" name="last_date" value="<?php echo $last_date; ?>">
			<input type="hidden" id="number_of_time_intervals" name="number_of_time_intervals" value="<?php echo $number_of_time_intervals; ?>">
			<input type="hidden" id="min_age" name="min_age" value="<?php echo $min_age; ?>">
			<input type="hidden" id="max_age" name="max_age" value="<?php echo $max_age; ?>">
			<input type="hidden" id="number_of_age_groups" name="number_of_age_groups" value="<?php echo $number_of_age_groups; ?>">

        
        

        <table class="data">

        <?php 
        for ($i = 0; $i <= $number_of_time_intervals; ++$i) {
            
            echo "<tr>";
            
            for ($j = 0; $j <= $number_of_age_groups; ++$j) {
                if ($i == 0) {
                    //Första raden
                    if ($j == 0) {
                        //Första cellen i första raden
                        echo "<th></th>";
                    }
                    elseif ($j == 1 && $j == $number_of_age_groups) {
                        //Bara en kolumn
                        echo "<th>$min_age - " . $max_age . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='$max_age'>";
                    }
                    elseif ($j == 1) {
                        //Andra cellen i första raden
                        echo "<th>$min_age - " . $ageArr[$j-1] . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='".$ageArr[$j-1]."'>";
                    }
                    elseif ($j == $number_of_age_groups) {
                        //Sista cellen i första raden
                        echo "<th>" . $ageArr[$j-2]+1 . " - " . $max_age . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='$max_age'>";
                    }                   
                    else {
                        echo "<th>" . $ageArr[$j-2]+1 . " - " . $ageArr[$j-1] . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='".$ageArr[$j-1]."'>";
                    }
                    
                }
                else if ($j == 0) {
                    //Första cellen (inte första raden)
                    if ($i == 1 && $i == $number_of_time_intervals) {
                        //Bara en rad
                        echo "<th>$first_date - " . $last_date . "</th>";
                        echo "<input type='hidden' id='date[]' name='date[]' value='$last_date'>";
                    }
                    else if ($i == 1) {
                        //Första cellen andra raden
                        echo "<th>$first_date - " . $dateArr[$i-1] . "</th>";
                        echo "<input type='hidden' id='date[]' name='date[]' value='".$dateArr[$i-1]."'>";
                    }
                    elseif ($i == $number_of_time_intervals) {
                        //Första cellen sista raden
                        
                        
                        $tmp_date=date_create($dateArr[$i-2]);
                        $tmp_date->modify('+1 day');
                        echo "<th>" . $tmp_date->format('Y-m-d') . " - " . $last_date . "</td>";
                        echo "<input type='hidden' id='date[]' name='date[]' value='$last_date'>";
                    }
                    else {
                        $tmp_date=date_create($dateArr[$i-2]);
                        $tmp_date->modify('+1 day');
                        
                        echo "<th>" . $tmp_date->format('Y-m-d') . " - " . $dateArr[$i-1] . "</th>";
                        echo "<input type='hidden' id='date[]' name='date[]' value='".$dateArr[$i-1]."'>";
                    }
                }
                else {
                    //Cell i mitten
                    echo "<td><input type='number' id='cost[$i][$j]' name='cost[$i][$j]' value='0' required> SEK</td>";
                }
            }
            echo "</tr>";
        }

             
        ?>
        </table>
        
        <br>
        
        	<input type="submit" value="Spara">
        </form>
            </div>
	
</body>

</html>