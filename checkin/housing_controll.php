<?php
 include_once 'header.php';
 
 include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}

</style>

<script src="../javascript/table_sort.js"></script>

    <div class="content">   
 		<?php 
 		$housing = House::getAllToCheck($current_larp);
 		if (empty($housing)) {
 		    echo "Inga klara för kontroll just nu.";
 		} else {
    	    $tableId = "housing";
    	    echo "<table id='$tableId' class='data'>";
    	    echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
    		    "<th onclick='sortTableNumbers(1, \"$tableId\")'>Typ</th>".
    		    "<th onclick='sortTableNumbers(2, \"$tableId\")'>Status tid</th>".
    	      "</tr>\n";
    	    foreach ($housing as $house)  {
    	        echo "<tr>\n";
    	        
    	        echo "<td>";
                echo "<a href='view_house.php?id=$house->Id'>$house->Name</a>";
    	        echo "</td>\n";
    	        
                echo "<td>";
                if ($house->IsHouse()) echo "Hus";
                else echo "Lägerplats";
    	        echo "</td>";       
    	            
                echo "<td>";
                $larp_house = Larp_House::loadByIds($house->Id, $current_larp->Id);
                if (isset($larp_house)) echo $larp_house->StatusTime;
                echo "</td>\n";
    	        echo "</tr>\n";
    	    }
    
    
    		echo "</table>";
 		}
		
    		?>

        
        
        
	</div>
</body>

</html>
