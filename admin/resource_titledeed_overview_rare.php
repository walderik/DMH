<?php
include_once 'header.php';

$titledeeds = Titledeed::allByCampaign($current_larp);
$resources = Resource::allRareByCampaign($current_larp);

$sums = array();
$currency = $current_larp->getCampaign()->Currency;

include 'navigation.php';
?>
<style>
th, td {
  border-style:solid;
  border-color: #d4d4d4;
  padding: 10px;
  margin: 0px;
  
}
</style>
    <div class="content">
        <h1>Lagfarter - ovanliga resurser - översikt</h1>
        <?php if (empty($resources) || empty($titledeeds)) {
            echo "Översikten kräver att det finns både resurser och lagfarter.";
            
        }
        else {
        ?>
        <table>
    	<tr>
    		<th></th>
    	<?php 
    	foreach ($resources as $key => $resource) {
    	    echo "<th><a href='resource_form.php?operation=update&Id=$resource->Id'>$resource->Name</a></th>\n";
    	    $sums[$key]=0;
    	}
    	?>
		</tr>
		
		<?php 
		
		foreach ($titledeeds as $titledeed) {
		    echo "<tr><th style='text-align:left'><a href='resource_titledeed_form.php?Id=$titledeed->Id'>$titledeed->Name</a></th>";
		    foreach ($resources as $key => $resource) {
		        $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
		        if (empty($resource_titledeed)) echo "<td style='text-align:right'>0</td>\n";
		        else {
		            echo "<td style='text-align:right'>$resource_titledeed->Quantity</td>\n";
		            $sums[$key] = $sums[$key] + $resource_titledeed->Quantity;
		        }
		    }
		    echo "</tr>\n";
		}
		
		echo "<tr><th style='text-align:left'>Summa</th>\n";
		foreach ($resources as $key => $resource) {
		    echo "<th style='text-align:right'>$sums[$key]</th>\n";
		}
		echo "</tr>\n";
		?>
		    
        
        
        </table>
        <?php }?>
    </body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>