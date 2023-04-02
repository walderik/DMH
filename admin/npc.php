<?php
include_once 'header.php';

include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>NPC</h1>
            <a href="npc_form.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC</a>  
            <a href="npc_group_form.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC grupp</a>  

            <div>
            <h2>Alla tilldelade NPC'er</h2>
            <?php 
            $persons=Person::getAllInterestedNPC($current_larp);
            
            
            
            
            $npc_groups = NPCGroup::getAllForLARP($current_larp);
            
            foreach ($npc_groups as $npc_group) {
                $npcs=NPC::getAllAssignedByGroup($npc_group, $current_larp);
                if (!empty($npcs)) {
                    echo "<h3>$npc_group->Name</h3>";
                }
                foreach($npcs as $npc) {
                    $person=$npc->getPerson();
                    $registration = $person->getRegistration($current_larp);
                    echo "<a href='npc_form.php?operation=update&id=$npc->Id'>$npc->Name <i class='fa-solid fa-pen'></i></a> \n";
                    echo "<a href='logic/delete_npc.php?id=$npc->Id'><i class='fa-solid fa-trash'></i></a> \n";
                    echo "$npc->Time<br>$npc->Description\n";
                    echo "Spelas av $person->Name: $registration->NPCDesire \n";
                    echo "<form action='logic/assign_npc.php' method='post'><input type='hidden' name='id' value='$npc->Id'>\n";
                    echo "<input type='hidden' name='PersonId' value='null'>\n";
                    echo "<input type ='submit' value='Ta bort från deltagaren'>\n";
                    echo "</form>\n";
                    echo "<br>\n<br>\n";
                }
            }
            
            ?>
            </div>
            
            <div>
            <h2>Alla NPC'er som inte är tilldelade</h2>
            <?php 
            
            $npc_groups = NPCGroup::getAllForLARP($current_larp);
            
            foreach ($npc_groups as $npc_group) {
                $npcs=NPC::getAllUnassignedByGroup($npc_group, $current_larp);
                if (!empty($npcs)) {
                    echo "<h3>$npc_group->Name</h3>";
                }

                foreach($npcs as $npc) {
                    echo "<a href='npc_form.php?operation=update&id=$npc->Id'>$npc->Name <i class='fa-solid fa-pen'></i></a> ";
                    echo "<a href='logic/delete_npc.php?id=$npc->Id'><i class='fa-solid fa-trash'></i></a> ";
                    echo "$npc->Time<br>$npc->Description";
                    echo "<form action='logic/assign_npc.php' method='post'><input type='hidden' name='id' value=$npc->Id>";
                    echo selectionDropDownByArray("PersonId", $persons);
                    echo "<input type ='submit' value='Tilldela'>";
                    echo "</form>";
                    echo "<br><br>"; 
                }
            }
 
            $npcs=NPC::getAllUnassignedByGroup(null, $current_larp);
            foreach($npcs as $npc) {
                echo "<a href='npc_form.php?operation=update&id=$npc->Id'>$npc->Name <i class='fa-solid fa-pen'></i></a> ";
                echo "<a href='logic/delete_npc.php?id=$npc->Id'><i class='fa-solid fa-trash'></i></a> ";
                echo "$npc->Time<br>$npc->Description";
                echo "<form action='logic/assign_npc.php' method='post'><input type='hidden' name='id' value=$npc->Id>";
                echo selectionDropDownByArray("PersonId", $persons);
                echo "<input type ='submit' value='Tilldela'>";
                echo "</form>";
                echo "<br><br>";
            }



            
            ?>
            </div>
            
            <div>
            <h2>Personer som vill vara NPC</h2>
            <?php 

            foreach($persons as $person) {
                $registration = $person->getRegistration($current_larp);
            
                echo "<a href='view_person.php?id=$person->Id'><strong>$person->Name</strong></a>, ";
                echo LarperType::loadById($person->LarperTypeId)->Name."<br>";
                echo "Roll(er): ";
                $roles = Role::getRegistredRolesForPerson($person, $current_larp);
                foreach ($roles as $role) {
                    echo "<a href='view_role.php?id=$role->Id'>$role->Name</a> ";
                }
                echo "<br>";
                echo "Önskemål: $registration->NPCDesire<br><br>";
            }
            ?>
            </div>
  	</div>
</body>

</html>
  