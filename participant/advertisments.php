<?php
include_once 'header.php';



include 'navigation.php';

$advetismentTypes = AdvertismentType::allActive($current_larp);
?>

    <div class="content"> 
    <h1>Annonser</h1>
    
    
    <?php 
    foreach ($advetismentTypes as $adtype) {
        echo "<h2>$adtype->Name</h2>";
        echo "<p>$adtype->Description</p>";
        $advertisments = Advertisment::allBySelectedLARPAndType($current_larp, $adtype);
        if (empty($advertisments)) {
            echo "Inga annonser av den här typen";
        } else {
            echo "<table class='data' id='ads' align='left'>";
            echo "<tr align='left'><th>Kontakt information</th><th>Text</th>";
            echo "</tr>\n";
            foreach ($advertisments as $advertisment) {
                echo "<tr>\n";
                echo "<td style='font-weight:normal'>$advertisment->ContactInformation</td>\n";
                echo "<td>$advertisment->Text</td>\n";
                echo "</tr>\n";
            }
            echo "</table></div>\n";
        }
        
        
    }
    
    
    
    
    ?>
    
    
    
    </div>


