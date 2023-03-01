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
    		    echo "Alla anmälda är godkända";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>Ålder</th><th>Epost</th><th>Mobilnummer</th><th></th><th>Godkänn</th></tr>\n";
    		    foreach ($persons as $person)  {
    		        echo "<tr>\n";
    		        echo "<td>" . $person->Name . "</td>\n";
    		        echo "<td>" . $person->getAgeAtLarp($current_larp) . " år</td>\n";
    		        echo "<td>" . $person->Email . "</td>\n";
    		        echo "<td>" . $person->PhoneNumber . "</td>\n";
    		        
    		        echo "<td>" . "<a href='view_person.php?id=" . $person->Id . "'><i class='fa-solid fa-eye'></i></a></td>\n";
    		        echo "</tr>\n";
    		        $roles = $person->getRolesAtLarp($current_larp);
    		        foreach($roles as $role) {
    		            echo "<tr>\n";
    		            echo "<td>&nbsp;&nbsp;&nbsp;" . $role->Name . "</td>\n";
    		            echo "<td>" . $role->Profession . "</td>\n";
    		            echo "<td>" . $role->getGroup()->Name . "</td>\n";
    		            echo "<td>";
    		            if (LARP_Role::loadByIds($role->Id, $current_larp->Id)->IsMainRole == 1) {
    		              echo "Huvudkaraktär";
    		            }
    		            echo "</td>\n";
    		            echo "<td>" . "<a href='view_role.php?id=" . $person->Id . "'><i class='fa-solid fa-eye'></i></a></td>\n";
    		            
    		            echo "</tr>\n";
    		            
    		        }
    		        echo "<tr></tr>";
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
