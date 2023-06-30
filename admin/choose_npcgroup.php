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

if ($operation == "add_intrigue_npcgroup") {
    $purpose = "Lägg till NPC-grupp till intrig";
    $url = "logic/view_intrigue_logic.php";
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
        <h1><?php echo $purpose;?></h1>
            <a href="npc_group_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Skapa ny NPC-grupp</a>  
     		<?php 
     		$npcgroups = NPCGroup::getAllForLarp($current_larp);
     		if (empty($npcgroups)) {
    		    echo "Inga registrerade NPC-grupper";
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
    		    <tr><th>Namn</th></tr>
    		    <?php 
    		    foreach ($npcgroups as $npcgroup)  {
    		        echo "<tr>\n";
    		        echo "<td><input type='$type' id='NPCGroup$npcgroup->Id' name='NPCGroupId$array' value='$npcgroup->Id'>";

    		        echo "<label for='NPC$npcgroup->Id'>$npcgroup->Name</label></td>\n";
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
