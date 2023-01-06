<?php
require 'header.php';


?>

        <nav id="navigation">
          <ul class="links">
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>


		<div class="content">
			<h2>Aktivt lajv</h2>
			<label for="larp">Välj lajv:</label>
			<form action="../includes/set_larp.php" method="POST">
    			<?php
    			 $larp_array = LARP::all();
    			 $resultCheck = count($larp_array);
    			 if ($resultCheck > 0) {
    			     echo "<select name='larp' id='larp'>";
    
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
			 <?php 
			 if (isset($_SESSION['admin'])) {
			 ?>
			     <a href="../admin/larp_form.php?operation=new" style="color: red"><i class="fa-solid fa-file-circle-plus"></i>Lägg till lajv</a>  
			 <?php 
			 }
			 ?>

	</body>
</html>