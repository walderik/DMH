<?php
include_once 'header.php';



include 'navigation.php';
?>


    <div class="content">   
        <h1>Välj intrigspår som ska fortsätta det här lajvet</h1>
     		<?php 
     		$intrigues = Intrigue::allNotContinuedInCampaign($current_larp);

    		 ?>
		<form action="logic/view_intrigue_logic.php" method="post">
		<input type="hidden" id="operation" name="operation" value="continue_intrigue"> 
		    <table class='data'>
		    <tr><th>Namn</th><th>Lajv senast använt</th><th>Ansvarig</th></tr>
		    <?php 
		    foreach ($intrigues as $intrigue)  {
		        echo "<tr>\n";
		        echo "<td><input type='checkbox' id='Intrigue$intrigue->Id' name='IntrigueId[]' value='$intrigue->Id'>";

		        echo "$intrigue->Name</td>\n";

		        echo "<td>".$intrigue->getLarp()->Name."</td>";
		        
		        echo "<td>";
		        $responsiblePerson = $intrigue->getResponsiblePerson();
		        if (isset($responsiblePerson)) echo $responsiblePerson->Name;
		        echo "</td>";
		        echo "</tr>\n";
		    }
		    echo "</table>";
		
    		?>
    		<br>
			<input type="submit" value="Välj"></form>
        
        
        
	</div>
</body>

</html>
