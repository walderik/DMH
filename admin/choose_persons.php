<?php
 include_once 'header.php';
 
 
 if ($_SERVER["REQUEST_METHOD"] == "POST") {
     
     $operation = $_POST['operation'];
     if (isset($_POST['id'])) {
         $id = $_POST['id'];
     } elseif (isset($_POST['Id'])) {
         $id = $_POST['Id'];
     }
 }
 if ($_SERVER["REQUEST_METHOD"] == "GET") {
     
     $operation = $_GET['operation'];
     if (isset($_GET['id'])) {
         $id = $_GET['id'];
     } elseif (isset($_GET['Id'])) {
         $id = $_GET['Id'];
     }
     
 }
 
$multiple=false;
 
 if ($operation == "officials") {
    $purpose = "funktionär"; 
    $url = "logic/official_save.php";
    $multiple=true;
 } elseif ($operation == "invoice_contact") {
    $purpose = "kontaktperson";
    $url = "logic/invoice_save.php";
    $multiple=false;
 } elseif ($operation == "invoice_add_concerns") {
     $purpose = "deltagare som fakturan gäller";
     $url = "logic/invoice_save.php";
     $multiple=true;
 }


 
 if ($multiple) {
     $type = "checkbox";
     $array="[]";
     
 } else {
     $type="radio";
     $array="";
 }
 
 
 if (isset($_SERVER['HTTP_REFERER'])) {
     $referer = $_SERVER['HTTP_REFERER'];
 }
 else {
     $referer = "";
 }
 
 include 'navigation.php';
?>


    <div class="content">   
        <h1>Lägg till <?php echo $purpose;?></h1>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp, false);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
    		} else {
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		    <input type="hidden" id="type" name="type" value="multiple">
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		    <input type='hidden' id='Id' name='Id' value='<?php echo $id ?>'>
   
    		    <table class='data'>
    		    <tr><th></th><th>Namn</th><th>Ålder på lajvet</th></tr>
    		    <?php 
    		    foreach ($persons as $person)  {
    		        echo "<tr>\n";
    		        
    		        echo "<td><input type='$type' id='Person$person->Id' name='PersonId$array' value='$person->Id'></td>";
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
