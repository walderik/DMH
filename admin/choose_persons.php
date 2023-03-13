<?php
 include_once 'header_subpage.php';
 
 if ($_SERVER["REQUEST_METHOD"] == "GET") {
     
     $operation = $_GET['operation'];
     
     if ($operation == "officials") {
      $purpose = "funktionär"; 
      $url = "logic/official_save.php";
     }     
 }

 if (isset($_SERVER['HTTP_REFERER'])) {
     $referer = $_SERVER['HTTP_REFERER'];
 }
 else {
     $referer = "";
 }
 
?>


    <div class="content">   
        <h1>Lägg till <?php echo $purpose;?></h1>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		    <input type="hidden" id="type" name="type" value="multiple">
    		<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		    <table class='data'>
    		    <tr><th></th><th>Namn</th><th>Ålder på lajvet</th></tr>
    		    <?php 
    		    foreach ($persons as $person)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='checkbox' id='PersonId$person->Id' name='PersonId[]' value='$person->Id'></td>";
    		        echo "<td>" . $person->Name . "</td>\n";
    		        echo "<td>" . $person->getAgeAtLarp($current_larp) . " år</td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="Lägg till som <?php echo $purpose;?>"></form>
        
        
        
	</div>
</body>

</html>
