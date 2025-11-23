<?php
include_once 'header.php';
include 'navigation.php';

$advetismentTypes = AdvertismentType::allActive($current_larp);
$now = new Datetime();
$current_person->AdvertismentsCheckedAt = date_format($now,"Y-m-d H:i:s");
$current_person->AdvertismentsCheckedAt = NULL;
$current_person->update();
?>
	<div class='itemselector'>
	<div class="header">
		<i class="fa-solid fa-bullhorn"></i> Annonser
	</div><br />
    <div class='itemcontainer'>Deltagarnas annonser inför lajvet.</br>Kontakta arrangörerna om du saknar en typ av annons som du vill lägga in.</div>
    <div class='itemcontainer'><a href='advertisment_form.php'><button class='button-18'><i class='fa-solid fa-plus'></i><i class='fa-solid fa-bullhorn'></i> &nbsp;Skapa en annons</button></a></div>
    
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
            echo "<tr align='left'><th>Annonsör</th><th>Kontakt information</th><th>Text</th>";
            echo "</tr>\n";
            foreach ($advertisments as $advertisment) {
                echo "<tr>\n";
                $person = $advertisment->getPerson();
                $name = (isset($person)) ? $person->Name : '';
                echo "<td style='border-bottom: 1px solid black;'>$name</td>\n";
                echo "<td style='border-bottom: 1px solid black;'>$advertisment->ContactInformation</td>\n";
                echo "<td style='border-bottom: 1px solid black;'>".nl2br(htmlspecialchars($advertisment->Text))."</td>\n";
                echo "</tr>\n";
            }
            echo "</table>\n";
        }
        echo "</div></br>";
        
        
    }
    
    
    
    
    ?>
    
    
    
    </div>


