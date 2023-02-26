<?php
require 'header.php';
include_once '../includes/error_handling.php';


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
                <li><a href="select_person.php">Deltagare</a></li>
                <li><a href="group_registration_form.php">Grupp</a></li>
              </ul>
            </li>
        	<?php 
        	 if (isset($_SESSION['admin'])) {
        	 ?>
        	     <li><a href="../admin/" style="color: red"><i class="fa-solid fa-lock"></i>Admin</a></li>  
        	 <?php 
        	 }
        	 ?>
        	<li><a href="help.php"><i class="fa-solid fa-circle-info"></i> Hjälp</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>
		<div class="content">
			<h1>Anmälan till <?php echo $current_larp->Name;?></h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
        	      echo '<div class="error">'.$error_message.'</div>';
        	  }?>
        	  <?php if (isset($message_message) && strlen($message_message)>0) {
        	      echo '<div class="message">'.$message_message.'</div>';
        	  }?>
			
			<div>
			Så här använder du anmälningssystemet:
				<ol>
			 	<li>Börja med att <a href="person_form.php">registrera en deltagare.</a></li>
			 	<li>Om du är gruppansvarig, <a href="group_form.php">registrera en grupp</a> och <a href="group_registration_form.php">anmäl den till lajvet</a>.</li>
			 	<li><a href="role_form.php">Skapa karaktärer</a>, gärna flera.</li>
			 	<li><a href="select_person.php">Anmäl deltagaren</a> till lajvet.</li>
			 	</ol>
			 	Det går att hantera flera deltagare från ett konto, tex om ni är en familj.<br><br>
			 	Till nästa lajv kommer alla registrerade deltagare, grupper och karaktärer att finnas kvar. Så då kan du bara kontrollera att allt ser rätt och sedan skicka in anmälan.
			 </div>
		</div>
		<div class="content">
    		<h2>Registreringar /anmälningar</h2>
    		<div>
    		<?php 
    		$persons = $current_user->getPersons();
    		if (empty($persons)) {
    		    echo "<a href='person_form.php'>Registrera en deltagare.</a>";
    		} else {
    		    foreach ($persons as $person)  {
    		        $roles = $person->getRoles();
    		        $groups = $person->getGroups();
    		        
    		        echo "<div class='person'>\n";
    		        
    		        if ($person->isRegistered($current_larp)) {
    		            echo "<h3>$person->Name&nbsp;<a href='view_person.php?id=" . $person->Id . "'><i class='fa-solid fa-eye'></i></a></h3>\n";    		            
    		        }
    		        else {
    		            echo "<h3>$person->Name&nbsp;<a href='person_form.php?operation=update&id=" . $person->Id . "'><i class='fa-solid fa-pen'></i></a></h3>\n";
    		        }
    		        echo "Epost: " . $person->Email. "<br>\n";
    		        echo "Mobilnummer: " . $person->PhoneNumber. "<br>\n";
    		        echo "<table>";
    		        if (isset($roles) && count($roles) > 0) {
                        echo "<tr><td>Anmäld</td><td>" . showStatusIcon($person->isRegistered($current_larp), "person_registration_form.php?PersonId=$person->Id"). "</td></tr>\n";
    		        }
                    if ($person->isRegistered($current_larp)) {
                        echo "<tr><td>Godkänd</td><td>" . showStatusIcon($person->isApproved($current_larp)). "</td></tr>\n";
                        echo "<tr><td>Betalat</td><td>" . showStatusIcon($person->hasPayed($current_larp));
                        if (!$person->hasPayed($current_larp)) {
                            $registration = Registration::loadByIds($person->Id, $current_larp->Id);
                            echo "</td><td>Betala " . $registration->AmountToPay . " SEK till xxxxxxxxxx ange referens: " . $registration->PaymentReference;
                        }
                        echo "</td></tr>\n";
 
                        
                    }
                    echo "<tr><td>Medlem</td><td>".showStatusIcon($person->isMember($current_larp->StartDate), "https://ebas.sverok.se/signups/index/5915")."</a>";
                    if (!$person->isMember($current_larp->StartDate)) {
                        echo "</td><td><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>Betala medlemsavgiften</a>";
                        echo "</td></tr>\n";
                    }
                    
                    echo "</table>";
    		        
    		        if (isset($groups) && count($groups) > 0) {
    		            echo "<br><b>Gruppansvarig för:</b><br>\n";
    		        }
    		        foreach ($groups as $group)  {
    		            if ($group->isRegistered($current_larp)) {
    		                echo $group->Name . " " . "<a href='view_group.php?id=" .
        		                $group->Id . "'><i class='fa-solid fa-eye'></i></a>";
        		                
    		            }
    		            else {
    		                echo $group->Name . " " . "<a href='group_form.php?operation=update&id=" . 
    		                 $group->Id . "'><i class='fa-solid fa-pen'></i></a>"; 
    		            }
    		            echo " Anmäld&nbsp;&nbsp;" . showStatusIcon($group->isRegistered($current_larp), "group_registration_form.php?new_group=$group->Id") . "<br>\n";
    		        }
    		       
    		        if (isset($roles) && count($roles) > 0) {
    		            echo "<br><b>Karaktärer:</b><br>\n";
    		        } else {
    		            echo "<br><b>Har ännu ingen karaktär</b>&nbsp;&nbsp;<a href='role_form.php'>".showStatusIcon(false)."</a><br>\n";
    		        }
    		        foreach ($roles as $role)  {
    		            $role_group = $role->getGroup();
    		            $role_group_name = "";
    		            if (isset($role_group)) {
    		                $role_group_name = " - $role_group->Name";
    		            }
    		            echo $role->Name . " - " . $role->Profession . " " . $role_group_name;
    		            if ($role->isRegistered($current_larp)) {
    		                echo " <a href='view_role.php?id=$role->Id'><i class='fa-solid fa-eye'></i></a><br>\n";
    		            }
    		            else {
        		            echo " <a href='role_form.php?operation=update&id=$role->Id'><i class='fa-solid fa-pen'></i></a><br>\n";
    		            }
    		            
    		        }
    		        echo "</div>\n";
    		    }
    		}
    		?>
    		</div>
		</div>
	</body>
</html>