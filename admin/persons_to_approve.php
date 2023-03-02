<?php

include_once 'header.php';


?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i>Hem</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>

    <div class="content">   
        <h1>Deltagare med karaktärer som ska godkännas</h1>
     		<?php 
    		$persons = Person::getAllToApprove($current_larp);
    		if (empty($persons)) {
    		    echo "<p>Alla anmälda är godkända</p>";
    		} else {
    		    echo "Kontrollera att all viktig information finns med och att alla roller fungerar på lajvet innan du godkänner. Om inte allt är ok, kontakta deltagaren och kom överens om förändringar.";
    		    foreach ($persons as $person)  {
    		        $registration = $person->getRegistration($current_larp);
    		        echo "<div>";
    		        echo "<form action='logic/approve_person.php' method='post'>";
    		        echo "<input type='hidden' id='RegistrationId' name='RegistrationId' value='$registration->Id'>";

    		        echo $person->Name.", ".$person->getAgeAtLarp($current_larp)." år ";
    		        echo "<a href='view_person.php?id=$person->Id'><i class='fa-solid fa-eye'></i></a> \n";
    		        echo "<a href='edit_person.php?id=$person->Id'><i class='fa-solid fa-pen'></i></a> \n";    		        echo "<br>\n";
    		        
    		        echo "Epost: $person->Email, Telefon: $person->PhoneNumber <br>\n";
    		        
    		        echo "<br>\n";
    		        echo "Roller<br>\n";
    		        $roles = $person->getRolesAtLarp($current_larp);
    		        foreach($roles as $role) {
    		            foreach ($roles as $role)  {
    		                $role_group = $role->getGroup();
    		                $role_group_name = "";
    		                if (isset($role_group)) {
    		                    $role_group_name = " - $role_group->Name";
    		                }
    		                echo $role->Name . " - " . $role->Profession . " " . $role_group_name;
        		            if (LARP_Role::loadByIds($role->Id, $current_larp->Id)->IsMainRole == 1) {
        		              echo " Huvudkaraktär";
        		            }
        		            echo "<a href='view_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye'></i></a>\n";
        		            echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a>\n";
    		            }
    		            echo "<br>\n";
    		            
    		        }
    		        echo "<br>\n";
    		        echo "<input type='submit' value='Godkänn'>";
    		        echo "</form>";
    		        echo "</div>";
    		    }

    		}
    		?>

        
        
        
	</div>
</body>

</html>
