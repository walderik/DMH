<?php
include_once 'header.php';



include 'navigation.php';

$advetismentTypes = AdvertismentType::allActive($current_larp);
?>
	<div class='itemselector'>
	<div class="header">
		<i class="fa-solid fa-bullhorn"></i> Annonser
	</div>
    <div class='center'>Deltagarnas annonser inför lajvet. <a href='advertisment_form.php'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-bullhorn'></i> &nbsp;Skapa en annons</button></a></div>
    
    <?php 
    foreach ($advetismentTypes as $adtype) {
        echo "<div class='itemcontainer' style='display:table'>";
        echo "<div class='itemname'>$adtype->Name</div>";
        echo "$adtype->Description</br><br>";
        
        $advertisments = Advertisment::allBySelectedLARPAndType($current_larp, $adtype);
        if (empty($advertisments)) {
            echo "<table id='ads' class='participant_table' style='width:100%;'>";
            echo "<tr align='left'><td style='border-bottom: 1px solid black;'>Inga annonser av den här typen ännu</td></tr>";
            echo "</table>\n";
        } else {
            echo "<table id='ads' class='participant_table' style='width:100%;'>";
            echo "<tr align='left'><th>Kontakt information</th><th>Text</th>";
            echo "</tr>\n";
            foreach ($advertisments as $advertisment) {
                echo "<tr>\n";
                echo "<td style='border-bottom: 1px solid black;'>$advertisment->ContactInformation</td>\n";
                echo "<td style='border-bottom: 1px solid black;'>$advertisment->Text</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        echo "</div></br>";
        
        
    }
    
    
    
    
    ?>
    
    
    
    </div>


