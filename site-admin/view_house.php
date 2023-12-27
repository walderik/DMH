<?php
include_once 'header.php';

$house = House::newWithDefault();

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $house = House::loadById($_GET['id']);
}


include "navigation.php";

?>
    
     
<style>

img {
  float: right;
}

ul.list {
    list-style-type: disc;
}
</style>

    <div class="content"> 
    	<h1><?php echo $house->Name; ?>
    	<a href='house_form.php?operation=update&id=<?php echo $house->Id ?>'><i class='fa-solid fa-pen'> </i></a>
    	</h1>
    	<table>
    		<tr>
    		<td>
        		<table>
        			<tr>
        				<td>Antal 
        				<?php 
        				if ($house->IsHouse()) echo "sovplatser"; 
        				else echo "tältplatser";
        				?>
        				</td>
        				<td>
     
        					<?php echo htmlspecialchars($house->NumberOfBeds); 
        					if ($house->IsHouse()) echo " (Hus)";
                            else echo " (Lägerplats)";?>
        				</td>
        				
        			</tr>
        			<tr>
        				<td>Plats</td>
        				<td>
        				<?php                 
        				if ($house->IsHouse()) echo "Hus";
                        else echo "Lägerplats";
        				?>
        				</td>
        			</tr>
        			<tr>
        				<td>Plats</td>
        				<td><?php echo nl2br(htmlspecialchars($house->PositionInVillage)); ?></td>
        			</tr>
        			<tr>
        				<td>Beskrivning</td>
        				<td><?php echo nl2br(htmlspecialchars($house->Description)); ?></td>
        			</tr>
        			<tr><td>&nbsp;</td></tr>
        			<tr>
        				<td colspan = '2'><h2>Husförvaltare är:</h2><br>
        					<table id='caretakers' class='data'>
            				<th>Namn</th><th>Medlem</th><th>&nbsp;</th>
            				<?php 
            				$caretakers = $house->getHousecaretaker();
            				foreach ($caretakers as $caretaker) {
            				    $person = $caretaker->getPerson();
            				    echo "<tr>\n";
            				    echo "  <td>$person->Name</td>\n";
            				    echo "  <td>".showStatusIcon($caretaker->isMember())."</td>";
            				    $txt = '"Är du säker '.$person->Name.' inte ska vara husförvaltare?"';
            				    $confirm = "onclick='return confirm($txt)'";
            				    echo "  <td><a href='logic/remove_caretaker.php?id=$person->Id&houseId=$house->Id' $confirm><i class='fa-solid fa-trash'></i></a></td>\n";
            				    echo "</tr>\n";
            				}
            				?>
        					</table>
        				</td>
        			</tr>
        			<tr><td>&nbsp;</td></tr>
        			<tr>
        				<td colspan = '2'><h2>Historik Boende:</h2><br>
        					<table id='caretakers' class='data'>
            				<th>Lajv</th><th>Start</th><th>Slut</th><th>Namn</th>
            				<?php 
            				$housings = $house->getAllHousing();
            				foreach ($housings as $housing) {
            				    $larp = $housing->getLarp();
            				    $person = $housing->getPerson();
            				    echo "<tr>\n";
            				    if (isset($larp)) {
            				        echo "  <td>$larp->Name</td>\n";
            				        echo "  <td>".substr($larp->StartDate, 2, 8)."</td>\n";
            				        echo "  <td>".substr($larp->EndDate, 2, 8)."</td>\n";
            				    } else {
            				        echo "<td> </td><td> </td><td> </td>";
            				    }
            				    
            				    echo "  <td>$person->Name</td>\n";
            				    echo "</tr>\n";
            				}
            				?>
        					</table>
        				</td>
        			</tr>
        		</table>
        	</td>
        	<?php 
	        if ($house->hasImage()) {
	            $image = $house->getImage();
	            $photografer = (!empty($image->Photographer) && $image->Photographer!="") ? "Fotograf $image->Photographer" : "";
	            echo "<td>";
	            echo "<img src='../includes/display_image.php?id=$house->ImageId' title='$photografer'/>\n";
	            echo "<br>$photografer";
	            echo "</td>";
	        }
            ?>
        	</tr>
    	</table>
    </body>

</html>