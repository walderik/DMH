<?php

include_once 'header.php';

include 'navigation.php';


?>


    <div class="content">   
        <h1>Grupper som ska godkännas</h1>
        <p>Ett godkännande gäller för kampanjen, inte bara det här lajvet. <br>
        Inför anmälan till varje lajv har gruppledaren möjlighet att ändra på gruppen. Då förlorar den automatiskt sitt godkännande.</p>
     		<?php 
    		$groups = Group::getAllToApprove($current_larp);
    		if (empty($groups)) {
    		    echo "<p>Alla anmälda är godkända</p>";
    		} else {
    		    foreach ($groups as $group)  {
    		        $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    		        echo "<div>";
    		        echo "<form action='logic/approve.php' method='post'>";
    		        echo "<input type='hidden' id='GroupId' name='GroupId' value='$group->Id'>";
    		        
    		        echo "<a href='view_group.php?id=$group->Id'>$group->Name</a>, Gruppledare ".$group->getPerson()->Name;
    		        echo "<br>\n";
    		        if ($larp_group->UserMayEdit == 1) {
    		            echo "Gruppledare får ändra på gruppen och kan därför inte godkännas.";
    		        } else {
    		            echo "<input type='submit' value='Godkänn gruppen'>";
    		        }
    		        echo "</form>";
    		        echo "</div>\n";
    		    }
    		}
    		?>    
        <h1>Karaktärer som ska godkännas</h1>
        <p>Ett godkännande gäller för kampanjen, inte bara det här lajvet. <br>
        Inför anmälan till varje lajv har deltagare möjlighet att ändra på karaktären. Då förlorar den automatiskt sitt godkännande.</p>
     		<?php 
     		$roles = Role::getAllToApprove($current_larp);
    		//$persons = Person::getAllToApprove($current_larp);
    		if (empty($roles)) {
    		    echo "<p>Alla anmälda är godkända</p>";
    		} else {
    		    foreach ($roles as $role)  {
    		        $person = $role->getPerson();
    		        //$registration = $person->getRegistration($current_larp);
    		        echo "<div>";
    		        echo "<form action='logic/approve.php' method='post'>";
    		        echo "<input type='hidden' id='RoleId' name='RoleId' value='$role->Id'>";
    		        
    		        $role_group = $role->getGroup();
    		        $role_group_name = "";
    		        if (isset($role_group)) {
    		            $role_group_name = " - $role_group->Name";
    		        }
    		        
    		        echo "<a href='view_role.php?id=" . $role->Id . "'>$role->Name</a> - $role->Profession $role_group_name";
    		        if (LARP_Role::loadByIds($role->Id, $current_larp->Id)->IsMainRole != 1) {
    		            echo ", Sidokaraktär";
    		        }
    		        echo "<a href='edit_role.php?id=" . $role->Id . "'><i class='fa-solid fa-pen'></i></a>\n";
    		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a>\n";
    		        echo "<br><br>";
    		        echo "Spelas av <a href='view_person.php?id=$person->Id'>$person->Name</a>, ".$person->getAgeAtLarp($current_larp)." år ";
    		        echo "<br>\n";
    		        echo "Epost: $person->Email, Telefon: $person->PhoneNumber <br>\n";
    		        
    		        if ($role->userMayEdit($current_larp)) {
    		            echo "Spelare får ändra på karaktären och därför kan den inte godkännas.";
    		        } else {
    		            echo "<input type='submit' value='Godkänn karaktären'>";
    		        }
    		        
    		        echo "</form>";
    		        echo "</div>";
    		    }

    		}
    		?>

        
        
        
	</div>
</body>

</html>
