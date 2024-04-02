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

if ($operation == "set_master") {
    $purpose = "Välj mästare";
    $url = "logic/view_magician_logic.php";
    $multiple=false;
} elseif ($operation == "add_spell_magician") {
    $purpose = "Lägg till magi till magiker";
    $url = "logic/view_magicspell_logic.php";
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
include 'magic_navigation.php';
?>


    <div class="content">   
        <h1><?php echo $purpose;?></h1>
     		<?php 
     		$magicians = Magic_Magician::allByCampaign($current_larp);
     		if (empty($magicians)) {
    		    echo "Ingen registrerad magiker";
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
    		    <tr><th>Namn</th><th>Nivå</th><th>Magiskola</th><th>Mästare</th></tr>
    		    <?php 
    		    foreach ($magicians as $magician)  {
    		        $role = $magician->getRole();
    		        $master = $magician->getMaster();
    		        $school = $magician->getMagicSchool();
    		        if (isset($master)) $masterRole = $master->getRole();
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Magician$magician->Id' name='MagicianId$array' value='$magician->Id'>";
    		        echo "<label for='Magician$magician->Id'>$role->Name</label></td>\n";
    		        echo "<td>" . $magician->Level . "</td>\n";
    		        echo "<td>";
    		        if (!empty($school)) echo $school->Name;
    		        echo "</td>\n";
    		        echo "<td>";
    		        if (isset($masterRole)) echo "$masterRole->Name";
    		        echo "</td>\n";
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
