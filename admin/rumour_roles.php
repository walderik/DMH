<?php

include_once 'header.php';

include 'navigation.php';
?>

<script src="../javascript/table_sort.js"></script>
	<div class="content">
	
		<h1>Huvudkaraktärer</h1>
    	<?php 
    	$roles = $current_larp->getAllMainRoles(false);
	    $tableId = "main_roles";
	    $colnum = 0;
	    echo "<table id='$tableId' class='data'>";
	    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";
	    echo "<th onclick='sortTableNumbers(". $colnum++ .", \"$tableId\")'>Antal rykten</th></tr>\n";
	    foreach ($roles as $role)  {
	        echo "<tr>\n";
	        echo "<td>";

	        echo $role->getViewLink();
	        echo "</td>\n";
	        echo "<td>";
	        $rumours = Rumour::allKnownByRole($current_larp, $role);
	        if (!empty($rumours)) echo sizeof($rumours);
	        echo "</td>\n";
	    }
        echo "</tr>\n";

	    echo "</table>";
        ?>
	    
	    <h1>Sidokaraktärer</h1>
	    <?php
	    $roles = $current_larp->getAllNotMainRoles(false);
	    $tableId = "main_roles";
	    $colnum = 0;
	    echo "<table id='$tableId' class='data'>";
	    echo "<tr><th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";
	    echo "<th onclick='sortTableNumbers(". $colnum++ .", \"$tableId\")'>Antal rykten</th></tr>\n";
	    foreach ($roles as $role)  {
	        echo "<tr>\n";
	        echo "<td>";
	        
	        echo $role->getViewLink();
	        echo "</td>\n";
	        echo "<td>";
	        $rumours = Rumour::allKnownByRole($current_larp, $role);
	        if (!empty($rumours)) echo sizeof($rumours);
	        echo "</td>\n";
	    }
	    echo "</tr>\n";
	    
	    echo "</table>";
	    
		?>
		    
		    

	</div>


</body>
</html>
