<?php

include_once 'header.php';

global $current_larp;


if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: index.php');
    exit;
    
}

$id = $_GET['Id'];

include 'navigation.php';
?>

	<div class="content">
		
		<?php 
    	?>
		<form action="logic/view_intrigue_logic.php" method="post" enctype="multipart/form-data">
		    <input type="hidden" id="operation" name="operation" value="add_intrigue_pdf">
		    <input type='hidden' id='Id' name='Id' value='<?php echo $id ?>'>    		
			<br><hr><br>
			Ladda upp en pdf som innehåller information relaterad till intrigen. <br>
			En aktör kan känna till en pdf. Då får aktören med pdf'en i intrigutskicket och kan även titta på den från sin sida.<br>
			Max storlek 5 MB och bara pdf:er.<br><br>
			<input type="file" name="bilaga" id="bilaga"><br>
	
    		<br><hr><br>
    		<input type="submit" value="Ladda upp">
		</form>

	</div>


</body>
</html>
