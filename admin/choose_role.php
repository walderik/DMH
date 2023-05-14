<?php
include_once 'header.php';

global $purpose;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];

    if ($operation == "set_prop_owner_role") {
        
        $purpose = "ägare av rekvisita";
        $url = "prop_owner_form.php";
    }
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
        <h1>Sätt som <?php echo $purpose;?></h1>
     		<?php 
    		$roles = Role::getAllRoles($current_larp);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    ?>
    		    <form action="<?php echo $url;?>" method="post">
    		    <input type="hidden" id="operation" name="operation" value="<?php echo $operation;?>">
    		    <?php 
    		    if (isset($_POST['id'])) {
    		        $id = $_POST['id'];
    		        echo "<input type='hidden' id='id' name='id' value='$id'>";
    		    }    
    		        ?> 
    		    <input type="hidden" id="type" name="type" value="single">
    			<input type="hidden" id="Referer" name="Referer" value="<?php echo $referer;?>">
    		    <table class='data'>
    		    <tr><th>Namn</th><th>Yrke</th><th>Grupp</th></tr>
    		    <?php 
    		    foreach ($roles as $role)  {
    		        echo "<tr>\n";
    		        
    		        echo "<td><input id ='Role$role->Id' type='radio' name='RoleId' value='$role->Id'>";
    		        echo "<label for='Role$role->Id'>$role->Name</label></td>\n";
    		        echo "<td>" . $role->Profession . "</td>\n";
    		        $group = $role->getGroup();
    		        if (is_null($group)) {
    		            echo "<td>&nbsp;</td>\n";
    		        } else {
    		            echo "<td>$group->Name</td>\n";
    		        }

    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
    		<br>
			<input type="submit" value="Sätt som <?php echo $purpose;?>"></form>
        
        
        
	</div>
</body>

</html>
