<?php
include_once 'header.php';

global $purpose;


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

if ($operation == "add_recipe_alchemist") {
    $purpose = "Lägg till recept till alkemister";
    $url = "logic/view_alchemist_logic.php";
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
include 'alchemy_navigation.php';
?>


    <div class="content">   
        <h1><?php echo $purpose;?></h1>
     		<?php 
     		$alchemists = Alchemy_Alchemist::allByCampaign($current_larp);
     		if (empty($alchemists)) {
    		    echo "Ingen registrerad alkemist";
    		} else {
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		    <?php 
    		    if (isset($id)) {
    		        echo "<input type='hidden' id='id' name='id' value='$id'>";
    		        echo "<input type='hidden' id='Id' name='Id' value='$id'>";
    		    }    
    		    ?> 
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		    <table class='data'>
    		    <tr><th>Namn</th><th>Nivå</th></tr>
    		    <?php 
    		    foreach ($alchemists as $alchemist)  {
    		        $role = $alchemist->getRole();
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Alchemist$alchemist->Id' name='AlchemistId$array' value='$alchemist->Id'>";
    		        echo "<label for='Alchemist$alchemist->Id'>$role->Name</label></td>\n";
    		        echo "<td>" . $alchemist->Level . "</td>\n";
    		        echo "</tr>\n";
    		    }
    		}
    		?>
    		</table>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>"></form>
        
        
        
	</div>
</body>

</html>
