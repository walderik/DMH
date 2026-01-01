<?php
include_once 'header.php';

include 'navigation.php';
include 'aktor_navigation.php';


if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}
?>


    <div class="content">   
    
            <h1>Grupper i kampanjen som inte är anmälda (än) i år</h1>
            Om man anmäler en grupp härifrån kommer de att få standardvärden på tex boendeönskemål. Du får redigera det på personen efteråt om det behövs.
            
     		<?php 
    		$groups = Group::getAllUnregisteredGroups($current_larp);
    		if (empty($groups)) {
    		    echo "Inga oanmälda grupper";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>Gruppledare</th><th>Senast spelad</th><th>Lägg till</th></tr>\n";
    		    foreach ($groups as $group)  {
    		        $person = $group->getPerson();
    		        echo "<tr>\n";
    		        echo "<td>";
					echo $group->getViewLink();
    		        echo "</td>\n";
    		        echo "<td>";
    		        if (!is_null($person)) echo $person->getViewLink();
    		        echo "</td>\n";
    		        $larp = $group->lastLarp();
    		        echo "<td>";
    		        if (empty($larp)) {
    		            echo "Aldrig spelad";
    		        }
    		        else {
    		            echo $larp->Name;
    		        }
    		        echo "</td>";
    		        echo "<td>\n";
    		        echo "<a href='logic/register_group.php?id=$group->Id'>Anmäl</a>";
    		        echo "</td>";
                    echo "</tr>\n";
		        }
    		    echo "</table>";
    		}
    		?>
    
    
    
    
        <h1>Karaktärer i kampanjen som inte är anmälda (än) i år</h1>
        <p>
        Om man anmäler någon härifrån kommer de att få standardvärden på tex boendeönskemål och matönskemål. Du får redigera det på personen efteråt om det behövs.
		<br><br>Deltagare som finns på reservlistan visas inte här.</p>
     		<?php 
    		$roles = Role::getAllUnregisteredRoles($current_larp);
    		$reserve_persons = Person::getAllReserves($current_larp);
    		if (empty($roles)) {
    		    echo "Inga anmälda karaktärer";
    		} else {
    		    echo "<table class='data'>";
    		    echo "<tr><th>Namn</th><th>Yrke</th><th>Grupp</th><th>Spelare</th><th>Senast spelad</th><th>Lägg till</th></tr>\n";
    		    foreach ($roles as $role)  {
    		        $person = $role->getPerson();
    		        if (!is_null($person) && !in_array($person, $reserve_persons)) {
        		        echo "<tr>\n";
        		        echo "<td>" . $role->getViewLink();
        		        echo "</td>\n";
        		        echo "<td>$role->Profession</td>\n";
        		        $group = $role->getGroup();
        		        if (is_null($group)) {
        		            echo "<td>&nbsp;</td>\n";
        		        } else {
							echo "<td>";
        		            echo $group->getViewLink();
							echo "</td>\n";
        		        }
        		        echo "<td>$person->Name</td>";
        		        $larp = $role->lastLarp();
        		        echo "<td>";
        		        if (empty($larp)) {
        		            echo "Aldrig spelad";
        		        }
        		        else {
        		            echo $larp->Name;
        		        }

    
        		        echo "<td>";
        		        if (($role->IsDead !=1)) {
            		        if ($person->isRegistered($current_larp)) {
            		            echo "<a href='logic/add_role.php?id=$role->Id'>Lägg till som sidokaraktär</a>";
            		        }
                            else {
                                echo "<a href='logic/register_person.php?id=$role->Id'>Anmäl med denna som huvudkaraktär</a>";
                            }
        		        }
                        echo "</td>";
                        echo "</tr>\n";

    		        }
      		    }
    		    echo "</table>";
    		}
    		?>
	</div>
</body>

</html>
