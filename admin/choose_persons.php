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

<script src="../javascript/table_sort.js"></script>

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
   
   
               <?php 
               
               $tableId = "persons";
               $colnum = 1;
               echo "<table id='$tableId' class='data'>";
               echo "<tr>".
                   "<th></th>".
                   "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>".
                   "<th onclick='sortTableNumbers(". $colnum++ .", \"$tableId\");'>Ålder på lajvet</th>";
               echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Grupp</th>".
                   "</tr>";
               
     		    foreach ($persons as $person)  {
    		        if ($operation == "invoice_add_concerns") {
    		            $registration = $person->getRegistration($current_larp);
    		            if ($registration->hasPayed()) continue;
    		        }
    		        echo "<tr>\n";
    		        
    		        echo "<td><input type='$type' id='Person$person->Id' name='PersonId$array' value='$person->Id'></td>";
    		        echo "<td>$person->Name</td>\n";
    		        echo "<td>" . $person->getAgeAtLarp($current_larp) . " år</td>\n";
    		        
    		        $role = $person->getMainRole($current_larp);
    		        $group = $role->getGroup();
    		        if (isset($group)) $groupName = $group->Name;
    		        else $groupName = "";
    		        echo "<td>" . $groupName . "</td>\n";
    		        
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
