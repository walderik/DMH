<?php

include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['PersonId'])) {
        $PersonId = $_POST['PersonId'];
    }
    else {
        header('Location: index.php');
        exit;
    }
}

$person = Person::loadById($PersonId);

if (!$person->isRegistered($current_larp)) {
    header('Location: index.php'); // Personen är inte anmäld
    exit;
}

$roles = $person->getRolesAtLarp($current_larp);

include 'navigation.php';

?>


	<div class="content">
		<h1>Byt huvudkaraktär för <?php echo $person->Name;?></h1>
		<div>

    		    <form action="logic/change_main_role_save.php" method="post">
    		      <input type='hidden' id='PersonId' name='PersonId' value='<?php echo $person->Id; ?>'>";
    		        
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
    		?>
    		<br>
			<input type="submit" value="Sätt som huvudkaraktär"></form>
        
