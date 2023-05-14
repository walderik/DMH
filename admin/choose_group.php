<?php
include_once 'header.php';

global $purpose;


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == "set_prop_owner_group") {
        
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

include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>Sätt som <?php echo $purpose;?></h1>
     		<?php 
    		$groups = Group::getAllRegistered($current_larp);
    		if (empty($groups)) {
    		    echo "Inga anmälda grupper";
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
    		    <tr><th>Namn</th></tr>
    		    <?php 
    		    foreach ($groups as $group)  {
    		        echo "<tr>\n";
    		        
    		        echo "<td><input id ='Group$group->Id' type='radio' name='GroupId' value='$group->Id'>";
    		        echo "<label for='Group$group->Id'>$group->Name</label></td>\n";

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
