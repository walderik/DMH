<?php
include_once 'header.php';

$titledeeds = Titledeed::allByCampaign($current_larp);
$resources = Resource::allNormalByCampaign($current_larp);

$sums = array();
$currency = $current_larp->getCampaign()->Currency;

include 'navigation.php';
?>
<style>
th, td {
  border-style:solid;
  border-color: #d4d4d4;
}
</style>
    <div class="content">
        <h1>Lagfarter - normala resurser - översikt</h1>
        <?php if (empty($resources) || empty($titledeeds)) {
            echo "Översikten kräver att det finns både resurder och lagfarter.";
            
        }
        else {
        ?>
        <table>
    	<tr>
    		<th></th>
    	<?php 
    	foreach ($resources as $key => $resource) {
    	    echo "<th>$resource->Name</th>\n";
    	    $sums[$key]=0;
    	}
    	?>
    	<th>Resultat</th>
		</tr>
		
		<?php 
		
		foreach ($titledeeds as $titledeed) {
		    echo "<tr><th style='text-align:left'>$titledeed->Name</th>";
		    foreach ($resources as $key => $resource) {
		        $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
		        if (empty($resource_titledeed)) echo "<td style='text-align:right'>0</td>\n";
		        else {
		            echo "<td style='text-align:right'>$resource_titledeed->Quantity</td>\n";
		            $sums[$key] = $sums[$key] + $resource_titledeed->Quantity;
		        }
		    }
		    echo "<td style='text-align:right'>".$titledeed->calculateResult($current_larp)." $currency</td>\n";
		    echo "</tr>\n";
		}
		
		echo "<tr><th style='text-align:left'>Summa</th>\n";
		foreach ($resources as $key => $resource) {
		    echo "<th style='text-align:right'>$sums[$key]</th>\n";
		}
		echo "<td></td>";
		echo "</tr>\n";
		?>
		    
        
        
        </table>
        <?php }?>
    </body>
<?php 
include_once '../javascript/table_sort.js';
?>
</html>