<?php

include_once 'header.php';
include_once '../includes/selection_data_control.php';

include 'navigation.php';
?>


	<div class="content">
		<h1>Sök karaktärer efter val</h1>
		<table>
		<tr>
		<td>Basdatatyp</td>
		<td>
		<?php 
		$types = getAllTypesForRoles();
		
		echo "<select name='selection_type' id='selection_type'>";
		foreach ($types as $key => $type) {
		    echo "<option value='$key'>$type</option>";
		}
		echo "</select>";
		
		
		?>
		
		
		
		</td>
		</tr>
		<tr>
		<td>Värde</td>
		<td>
		</td>
		</tr>
		<tr>
		<td></td><td><button>Sök</button></td></tr>
		</table>
	
	

	</div>


</body>
</html>
