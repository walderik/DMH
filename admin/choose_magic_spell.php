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

if ($operation == "add_school_spell") {
    $purpose = "Lägg till magi till magiskola";
    $url = "logic/view_magicschool_logic.php";
    $multiple=true;
} elseif ($operation == "add_magician_spell") {
    $purpose = "Lägg till magi till magiker";
    $url = "logic/view_magician_logic.php";
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
            <a href="magic_spell_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Skapa ny magi</a>  
     		<?php 
     		$spells = Magic_Spell::allByCampaign($current_larp);
     		if (empty($spells)) {
    		    echo "Ingen registrerad magi";
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
    		    <tr><th>Namn</th><th>Nivå</th><th>Typ</th></tr>
    		    <?php 
    		    foreach ($spells as $spell)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='Spell$spell->Id' name='SpellId$array' value='$spell->Id'>";

    		        echo "<label for='Spell$spell->Id'>$spell->Name</label></td>\n";

    		        echo "<td>$spell->Level</td><td>".Magic_Spell::TYPES[$spell->Type]."</td>";
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
