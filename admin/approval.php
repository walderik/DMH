<?php

include_once 'header.php';

include 'navigation.php';


?>


    <div class="content">   
        <h1>Grupper som ska godkännas</h1>
        <p>Ett godkännande gäller för kampanjen, inte bara det här lajvet. <br>
        Inför anmälan till varje lajv har gruppledaren möjlighet att ändra på gruppen. Då förlorar den automatiskt sitt godkännande.<br>
        Om samma gruppen (i fiktionen) redan har <a href='not_registered_roles.php'>funnits i kampanjen</a> ska den användas istället för att skapa en ny. 
        Annars följer inte länkar i intrigspår och "Vad hände" med som det ska. </p>
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
    		        
    		        echo $group->getViewLink();
    		        $groupLeader = $group->getPerson();
    		        if (!is_null($groupLeader)) echo ", Gruppledare ".$groupLeader->Name;
    		        echo "<br>\n";
    		        if (empty($group->getPreviousLarps($current_larp))) echo "Helt ny grupp.<br>";
    		        $oldApprovedGroup = $group->getOldApprovedGroup();
    		        if (!empty($oldApprovedGroup)) echo "Gruppen har tidigare varit godkänd. <a href='view_group_changes.php?id=$group->Id'>Visa ändringar</a><br>";
    		        
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
        Inför anmälan till varje lajv har deltagare möjlighet att ändra på karaktären. Då förlorar den automatiskt sitt godkännande.<br>
        Om samma karaktär (i fiktionen) redan har <a href='not_registered_roles.php'>funnits i kampanjen</a> ska den användas istället för att skapa en ny. 
        Annars följer inte länkar i intrigspår och "Vad hände" med som det ska. </p>
     		<?php 
     		$roles = Role::getAllToApprove($current_larp);
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
    		        

    		        echo $role->getViewLink() . " - " . $role->Profession . " " . $role_group_name;
    		        if (LARP_Role::loadByIds($role->Id, $current_larp->Id)->IsMainRole != 1) {
    		            echo ", Sidokaraktär";
    		        }
					echo " ";
    		        echo $role->getEditLinkPen(true);
					echo " ";


    		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a><br>\n";
    		        if (empty($role->getPreviousLarps($current_larp))) echo "Helt ny karaktär.<br>";
    		        $oldApprovedRole = $role->getOldApprovedRole();
    		        if (!empty($oldApprovedRole)) echo "Karaktären har tidigare varit godkänd. <a href='view_role_changes.php?id=$role->Id'>Visa ändringar</a><br>";
    		        echo "<br><br>";
    		        if (!is_null($person)) {
        		        echo "Spelas av ", $person->getViewLink(), ", ".$person->getAgeAtLarp($current_larp)." år ";
        		        echo "<br>\n";
        		        echo "Epost: $person->Email, Telefon: $person->PhoneNumber <br>\n";
    		        } else {
    		            $creator = $role->getCreator();
    		            echo "NPC<br>\n";
    		            if (!is_null($creator)) {
        		            echo "Skapad av av ", $creator->getViewLink();
            		        echo "<br>\n";
            		        echo "Epost: $creator->Email, Telefon: $creator->PhoneNumber <br>\n";  
    		            }
    		        }
    		        
    		        if ($role->userMayEdit()) {
    		            echo "Spelare får ändra på karaktären och därför kan den inte godkännas.";
    		        } else {
    		            echo "<input type='submit' value='Godkänn karaktären'>";
    		        }
    		        
    		        echo "</form>";
    		        echo "</div>";
    		    }

    		}
    		?>

        <h1>NPC'er som ska godkännas</h1>
        <p>Ett godkännande gäller för kampanjen, inte bara det här lajvet. </p>
     		<?php 
     		$roles = Role::getAllNPCToApprove($current_larp);
    		if (empty($roles)) {
    		    echo "<p>Det finns inga att godkänna</p>";
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
    		        

    		        echo $role->getViewLink() . " - " . $role->Profession . " " . $role_group_name;
    		        if ($role->isPC() && LARP_Role::loadByIds($role->Id, $current_larp->Id)->IsMainRole != 1) {
    		            echo ", Sidokaraktär";
    		        }
					echo " ";
    		        echo $role->getEditLinkPen(true);
					echo " ";


    		        echo "<a href='character_sheet.php?id=" . $role->Id . "' target='_blank'><i class='fa-solid fa-file-pdf' title='Karaktärsblad för $role->Name'></i></a><br>\n";
    		        if (empty($role->getPreviousLarps($current_larp))) echo "Helt ny karaktär.<br>";
    		        $oldApprovedRole = $role->getOldApprovedRole();
    		        if (!empty($oldApprovedRole)) echo "Karaktären har tidigare varit godkänd. <a href='view_role_changes.php?id=$role->Id'>Visa ändringar</a><br>";
    		        echo "<br><br>";
    		        if (!is_null($person)) {
        		        echo "Spelas av ", $person->getViewLink(), ", ".$person->getAgeAtLarp($current_larp)." år ";
        		        echo "<br>\n";
        		        echo "Epost: $person->Email, Telefon: $person->PhoneNumber <br>\n";
    		        } else {
    		            $creator = $role->getCreator();
    		            if (!is_null($creator)) {
        		            echo "Skapad av av ", $creator->getViewLink();
            		        echo "<br>\n";
            		        echo "Epost: $creator->Email, Telefon: $creator->PhoneNumber <br>\n";  
    		            }
    		        }
    		        
    		        if ($role->userMayEdit()) {
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
