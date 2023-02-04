 <?php
 include_once 'header.php';
 

?>

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>

    <div class="content">   
        <h1>Anmälda deltagare</h1>
     		<?php 
    		$persons = Person::getAllRegistered($current_larp);
    		if (empty($persons)) {
    		    echo "Inga anmälda deltagare";
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
    		            
    		            echo "<td>" . "<a href='show_role.php?id=" . $role->Id . "'><i class='fa-solid fa-eye'></i></td>\n";
    		            echo "<td>" . "<a href='../participant/role_form.php?operation=update&id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
    		            echo "</tr>\n";
    		            
    		        }
    		    }
    		    echo "</table>";
    		}
    		?>

        
        
        
	</div>
</body>

</html>
