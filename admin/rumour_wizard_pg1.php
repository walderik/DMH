<?php
include_once 'header.php';
include 'navigation.php';
?>


    <div class="content">
        <h1>Slumpmässig fördelning av rykten - sida 1 av 3</h1>
        <?php 
        $rumour_array = Rumour::allApprovedUnknown($current_larp);
         
        if (empty($rumour_array)) {           
        ?>
        Det finns inga rykten att sprida.<br>För att ett rykte ska kunna spridas måste det vara godkänt och inte känt av någon.<br>
        
        <?php 
        exit;
        }?>
      <p>Den här guiden kommer att hjälpa dig att enkelt sprida rykten till slumpvis valda karaktärer.<br>
      Rykten kommer inte att spridas till karaktärer och grupper som inte är godkända. Inte heller till karaktärer som har valt att vara bakgrundslajvare.<br><br>
         
        <form action="rumour_wizard_pg2.php" method="post" >
	        <h2>Välj vilka rykten som ska spridas</h2>
		    <table class='data'>
        	    <tr><th>Namn</th></tr>
        	    <?php 
        	    foreach ($rumour_array as $rumour)  {
        	        echo "<td><input type='checkbox' id='Rumour$rumour->Id' name='RumourId[]' value='$rumour->Id'>";
        
        	        echo "<label for='Rumour$rumour->Id'>$rumour->Text</label></td>\n";
        	        
        	        echo "</tr>\n";
        	    }
        	    ?>
    	    </table>
			<br>
        
        	<input type="submit" value="Nästa">
        </form>
    </div>
	
</body>

</html>