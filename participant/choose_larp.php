<?php
require 'header.php';


?>

<nav id="navigation">
  <a href="#" class="logo">Studio<span>+<span></a>
  <ul class="links">
    <li><a href="#">About</a></li>
    <li class="dropdown"><a href="#" class="trigger-drop">Work<i class="arrow"></i></a>
      <ul class="drop">
        <li><a href="#">Art</a></li>
        <li><a href="#">Photography</a></li>
        <li><a href="#">Audio</a></li>
        <li><a href="#">Films</a></li>
      </ul>
    </li>
    <li class="dropdown"><a href="#" class="trigger-drop">Contact<i class="arrow"></i></a>
      <ul class="drop">
        <li><a href="#">Email</a></li>
        <li><a href="#">Phone</a></li>
      </ul>
    </li>
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