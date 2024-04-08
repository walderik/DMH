<?php
include_once 'header.php';
include 'navigation.php';
?>


    <div class="content">
        <h1>Slumpmässig fördelning av syner</h1>

        <?php 
        $vision_array = Vision::allNotHas($current_larp);
         
        if (empty($vision_array)) {           
        ?>
        <p>Det finns inga syner att slumpa ut.<br>För att en syn ska kunna slumpas ut får det inte redan vara tilldelat till någon.</p>
        
        <?php 
        exit;
        }?>
        <p>Syner fördelas inte till karaktärer som inte är godkända och inte till karaktärer som är bakgrundslajvare.</p>
        <form action="logic/vision_random_save.php" method="post" >
	        <h2>Välj vilka syner som ska slumpas ut</h2>
		    <table class='data'>
        	    <tr><th>Namn</th></tr>
        	    <?php 
        	    foreach ($vision_array as $vision)  {
        	        echo "<td><input type='checkbox' id='Vision$vision->Id' name='VisionId[]' value='$vision->Id' checked='checked'>";
        
        	        echo "<label for='Vision$vision->Id'>$vision->VisionText</label></td>\n";
        	        
        	        echo "</tr>\n";
        	    }
        	    ?>
    	    </table>
			<br>
        <h2>Vilka ska fördelningen av syner begränsas till?</h2>
			<?php 
			if (Ability::isInUse($current_larp)) {
			?>

			<div class="question">
				Förmågor
				<br> 
                <?php Ability::selectionDropdown($current_larp, false, false); ?>
			</div>
        	<?php } ?>
             <h2>Hur sannolikhet att varje karaktär ska få synen?</h2>
			<div class="question">
				<input type="number" id="percent" name="percent" value="50" style='text-align:right' required> %
			</div>
          
        	<input type="submit" value="Slumpa">
        </form>
    </div>
	
</body>

</html>