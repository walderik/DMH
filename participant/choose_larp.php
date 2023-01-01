<?php
require 'header.php';


?>
		<div class="content">
			<h2>Aktivt lajv</h2>
			<label for="larp">Välj lajv:</label>
			<form action="../includes/set_larp.php" method="POST">
    			<?php
    			 $larp_array = LARP::all();
    			 $resultCheck = count($larp_array);
    			 if ($resultCheck > 0) {
    			     echo "<select name='larps' id='larps'>";
    
    			     foreach ($larp_array as $larp) {
    			         echo "<option value='" . $larp->Id . "'>". $larp->Name . "</option>\n";
    			     }
    			     echo "</select>";
    			 }
    			 else {
    			     echo "<p>Inga registrarade ännu</p>";
    			 }
    			 ?>
    			 <input type="submit" value="Välj">
			 </form>
			 </div>
	</body>
</html>