<?php
require 'header.php';
include_once '../includes/error_handling.php';


function showStatusIcon($text) {
    if ($text == "Ja") {
        return '<img src="../images/ok-icon.png" alt="OK" width="30" height="30">';
    }
    if ($text == "Nej") {
        return '<img src="../images/alert-icon.png" alt="Varning" width="30" height="30">';
    }
}


?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
              <li class="dropdown"><a href="#" class="trigger-drop">Registrera<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="person_form.php">Deltagare</a></li>
                <li><a href="role_form.php">Karaktär</a></li>
                <li><a href="group_form.php">Grupp</a></li>
              </ul>
            </li>
          <ul class="links">
              <li class="dropdown"><a href="#" class="trigger-drop">Anmäl<i class="arrow"></i></a>
              <ul class="drop">
                <li><a href="person_signup.php"></i>Deltagare</a></li>
                <li><a href="group_signup.php"></i>Grupp</a></li>
              </ul>
            </li>
        	<?php 
        	 if (isset($_SESSION['admin'])) {
        	 ?>
        	     <li><a href="../admin/" style="color: red"><i class="fa-solid fa-lock"></i>Admin</a></li>  
        	 <?php 
        	 }
        	 ?>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>
		<div class="content">
			<h1>Anmälan till <?php echo $current_larp->Name;?></h1>
		</div>
	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
		<div class="content">
    		<h1>Deltagarna du hanterar</h1>
    		<p>
    		<?php 
    		$persons = $current_user->getPersons();
    		if (empty($persons)) {
    		    echo "<a href='person_form.php'>Registrera en deltagare.</a>";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>Epost</th><th>Ålder på lajvet</th><th>Mobilnummer</th><th></th><th>Anmäld</th><th>Godkänd</th><th>Medlem</th></tr>\n";
    		    foreach ($persons as $person)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $person->Name . "</td>\n";
    		        echo "<td>" . $person->Email . "</td>\n";
    		        echo "<td>" . $person->getAgeAtLarp($current_larp->StartDate) . "</td>\n";
    		        echo "<td>" . $person->PhoneNumber . "</td>\n";
    		        
    		        echo "<td>" . "<a href='person_form.php?operation=update&id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
    		        echo "<td align='center'>" . showStatusIcon($person->isRegistered($current_larp)) . "</td>\n";
    		        echo "<td align='center'>" . showStatusIcon($person->isApproved($current_larp)) . "</td>\n";
    		        echo "<td align='center'>" . showStatusIcon($person->isMember($current_larp->StartDate)) . "</td>\n";
    		        echo "</tr>\n";
    		        $roles = $person->getRoles();
    		        foreach ($roles as $role)  {
    		            echo "<tr>\n";
    		            echo "<td></td>\n";
    		            echo "<td>" . $role->Name . "</td>\n";
    		            echo "<td>" . $role->Profession . "</td>\n";
    		            
    		            echo "<td>" . "<a href='role_form.php?operation=update&id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
    		            echo "</tr>\n";
    		            
    		        }
    		    }
    		    echo "</table>";
    		}
    		?>
    		</p>
		</div>
	</body>
</html>