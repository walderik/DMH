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
    
    if (isset($_POST['food_description'])) $food_descriptionArr = $_POST['food_description'];    
    if (isset($_POST['date'])) $dateArr = $_POST['date'];
    if (isset($_POST['age'])) $ageArr = $_POST['age'];
    
} else {
    header('Location: payment_information_admin.php');
    exit;
}
    

include 'navigation.php';

?>

<style>
input {
  width: 60px;
  text-align: right; 
}

div.food {
	border-top: 1px solid #808080;
	padding-top: 6px;
}
div.cost {
  height: 2em;
}
</style>



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
			<input type="hidden" id="number_of_food_options" name="number_of_food_options" value="<?php echo $number_of_food_options; ?>">
			<?php 
			if (!empty($food_descriptionArr)) {
    			foreach ($food_descriptionArr as $food_description) {
    			    echo "<input type='hidden' id='food_description[]' name='food_description[]' value='$food_description'>";
    			}
			}
			?>
        
        

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
                        echo "<th colspan='2'>$min_age - " . $max_age . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='$max_age'>";
                    }
                    elseif ($j == 1) {
                        //Andra cellen i första raden
                        echo "<th colspan='2'>$min_age - " . $ageArr[$j-1] . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='".$ageArr[$j-1]."'>";
                    }
                    elseif ($j == $number_of_age_groups) {
                        //Sista cellen i första raden
                        echo "<th colspan='2'>" . $ageArr[$j-2]+1 . " - " . $max_age . " år</th>";
                        echo "<input type='hidden' id='age[]' name='age[]' value='$max_age'>";
                    }                   
                    else {
                        echo "<th colspan='2'>" . $ageArr[$j-2]+1 . " - " . $ageArr[$j-1] . " år</th>";
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
                    echo "<td>";
                    if ($number_of_food_options > 0) echo "<div class='cost'>Deltagaravgift:</div>";
                    if ($number_of_food_options > 0) {
                        echo "<div class='food'>Matkostnad:</div>";
                        for ($k = 0; $k < $number_of_food_options; ++$k) {
                            echo "<div class='cost'>".$food_descriptionArr[$k]."</div>";
                        }
                    }
                    echo "</td><td>";                   
                    
                    echo "<div class='cost'><input type='number' id='cost[$i][$j]' name='cost[$i][$j]' value='0' size='4' required> SEK</div>";
                    if ($number_of_food_options > 0) {
                        echo "<div class='food'>&nbsp;</div>";
                        for ($k = 0; $k < $number_of_food_options; ++$k) {
                            echo "<div class='cost'><input type='number' id='food_cost[$i][$j][$k]' name='food_cost[$i][$j][$k]' value='0' size='4' required> SEK</div>";
                        }
                    }
                    echo "</td>";
                }
            }
            echo "</tr>";
        }

             
        ?>
        </table>
        
        <br>
        
        	<button type="submit">Spara</button>
        </form>
            </div>
	
</body>

</html>