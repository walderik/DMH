<?php
include_once 'header.php';
include 'navigation.php';
?>


    <div class="content">
        <h1>Slumpmässig fördelning av rykten - sida 1 av 3</h1>
        <p>Den här guiden kommer att hjälpa dig att enkelt sprida rykten till slumpvis valda karaktärer.<br><br>
        <?php 
        $rumour_array = Rumour::allApprovedBySelectedLARP($current_larp);
        $unknown_rumour_array = array();
        foreach ($rumour_array as $rumour) {
            if ($rumour->getKnowsCount() > 0) continue;
            $unknown_rumour_array[] = $rumour;
        }
        
        if (!empty($unknown_rumour_array)) {           
        ?>
        Det finns inga rykten att sprida.<br>För att ett rykte ska kunna spridas måste det vara godkänt och inte känt av någon.<br>
        
        <?php 
        }?>
         
        <form action="rumour_wizard_pg2.php" method="post" >
	        <h2>Välj vilka rykten som ska spridas</h2>
		    <table class='data'>
        	    <tr><th>Namn</th></tr>
        	    <?php 
        	    foreach ($unknown_rumour_array as $rumour)  {
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