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

if ($operation == "add_intrigue_actor_subdivision") {  
    $purpose = "Lägg till aktör i intrig";
    $url = "logic/view_intrigue_logic.php";
    $multiple=true;
}
elseif ($operation == "exhange_intrigue_actor_subdivision") {
    $purpose = "Byt aktör på intrig";
    $url = "logic/view_intrigue_logic.php";
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
        <h1><?php echo $purpose;?></h1>
     		<?php 
    		$subdivisions = Subdivision::allByCampaign($current_larp);
    		if (empty($subdivisions)) {
    		    echo "Det finns inga grupperingar";
    		} else {
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		    <?php 
    		    if (isset($id)) {
    		        echo "<input type='hidden' id='id' name='id' value='$id'>";
    		        echo "<input type='hidden' id='Id' name='Id' value='$id'>";
    		    }    
    		    if (isset($_POST['2ndReferer'])) {
    		        echo "<input type='hidden' id='2ndReferer' name='2ndReferer' value='".$_POST['2ndReferer']."'>";
    		    }
    		    
    		    
    		    ?> 
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		    <table class='data'>
    		    <tr><th>Namn</th>
    		    </tr>
    		    <?php 
    		    foreach ($subdivisions as $subdivision)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Subdivision$subdivision->Id' name='SubdivisionId$array' value='$subdivision->Id'>";

    		        echo "<label for='Subdivision$subdivision->Id'>$subdivision->Name</label></td>\n";
    		        
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="<?php echo $purpose;?>"></form>
        
        
        
	</div>
</body>

</html>
