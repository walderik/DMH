<?php
require 'header.php';


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
		<div class="registred_stuffs">
    		<h1>Deltagarna du hanterar</h1>
    		<?php 
    		$persons = $current_user->getPersons();
    		if (empty($persons)) {
    		    echo "<a href='person_form.php'>Registrera en deltagare.</a>";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>Epost</th><th>Personnummer</th><th>Mobilnummer</th><th></th></tr>\n";
    		    foreach ($persons as $person)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $person->Name . "</td>\n";
    		        echo "<td>" . $person->Email . "</td>\n";
    		        echo "<td>" . $person->SocialSecurityNumber . "</td>\n";
    		        echo "<td>" . $person->PhoneNumber . "</td>\n";
    		        
    		        echo "<td>" . "<a href='person_form.php?operation=update&id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
    		        echo "</tr>\n";
    		    }
    		    echo "</table>";
    		}
    		?>
		</div>
	</body>
</html>