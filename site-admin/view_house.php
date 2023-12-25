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
        			<tr>
        				<td colspan = '2'><b>Husförvaltare är:</b><br>
        				<p>
        				<?php 
        				$caretakers = $house->getCaretakers();
        				foreach ($caretakers as $person) {
        				    echo "$person->Name<br>";
        				}
        				?>
        				</p>
        				</td>
        			</tr>
        			<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
        		</table>
        	</td>
        	<?php 
	        if ($house->hasImage()) {
	            $image = $house->getImage();
	            echo "<td>";
	            echo "<img src='../includes/display_image.php?id=$house->ImageId'/>\n";
	            if (!empty($image->Photographer) && $image->Photographer!="") echo "<br>Fotograf $image->Photographer";
	            echo "</td>";
	        }
            ?>
        	</tr>
    	</table>
    </body>

</html>