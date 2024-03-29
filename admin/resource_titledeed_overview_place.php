<?php
include_once 'header.php';

$resources = Resource::allNormalByCampaign($current_larp);
$places = TitledeedPlace::allActive($current_larp);

//$currency = $current_larp->getCampaign()->Currency;

include 'navigation.php';
?>
<style>
th, td {
  border-style:solid;
  border-color: #d4d4d4;
  padding: 10px;
  margin: 0px;
}

input[type="number"].produces {
background-color: lightgreen;
}

input.requires {
background-color: lightcoral;
}

input {
  width: 60px;
  text-align: right; 
}

</style>

 <style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>


    <div class="content">
        <h1>Resursöversikt - platser <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till handel"></i></a></h1>
		<input type="hidden" id="Currency" value="<?php //echo $currency ?>">
        <?php if (empty($resources) || empty($places)) {
            echo "Översikten kräver att det finns både resurser och platser.";
            
        }
        else {
        ?>

        <table>
    	<tr>
    		
    	<?php
    	echo "<th>";
    	//echo "Namn";
    	//echo "<br>$currency/st";
    	echo "</th>";
    	//echo "<th>$currency</th>\n";
    	foreach ($resources as $resource) {
    	    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a>";
    	    //echo "<br>$resource->Price";
    	    echo "</th>\n";
    	}
    	?>
    	<!-- <th>Resultat</th>  -->
		</tr>
		
		<?php 
		
		foreach ($places as $place) {
		    echo "<tr><td style='text-align:left'>$place->Name</td>";
		    //echo "<th valign='top' style='text-align:left'>";
		    //echo $place->getMoney();
		    //echo "</th>\n";
		    
		    foreach ($resources as $resource) {
		        $quantity = Resource_Titledeed::getResourceAmountForPlace($resource->Id, $place->Id);
	            echo "<td style='text-align:right'>";
	            echo $quantity;
	            echo "</td>\n";
		    }
		    //echo "<td>";
		    //echo $place->calculateResult();
		    //echo " $currency</td>\n";
		    echo "</tr>\n";
		}
		
		
		
		echo "<tr><th style='text-align:left'>Balans</th>\n";
		//echo "<th style='text-align:right' id='Money_sum'>";
		//echo $place->moneySum($current_larp);
		//echo "</th>\n";
		foreach ($resources as $resource) {
		    echo "<th style='text-align:right' id='Balance_$resource->Id'>".$resource->countBalance($current_larp)."</th>\n";
		}
		//echo "<td></td>";
		//echo "<tr><th style='text-align:left'>Antal kort</th>\n";
		//echo "<th></th>\n";
		//foreach ($resources as $resource) {
		    echo "<th style='text-align:right' id='Cards_$resource->Id' >".$resource->countNumberOfCards($current_larp)."</th>\n";
		//}
		//echo "<td></td>";
		//echo "</tr>\n";

		echo "<tr>\n";
		echo "<th></th>\n";
		//echo "<th>$currency</th>\n";
		foreach ($resources as $key => $resource) {
		    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a>";
		    //echo "<br>$resource->Price";
		    echo "</th>\n";
		}
		echo "</tr>\n";
		
		
		?>
		    
        
        
        </table>
        <?php }?>
    </body>
    

</html>