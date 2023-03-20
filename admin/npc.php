<?php
include_once 'header.php';

include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>NPC</h1>
            <a href="create_npc.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC</a>  
            <a href="create_npc_group.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC grupp</a>  

            <div>
            <h2>Alla tilldelade NPC'er</h2>
            <?php 
            /*
            $npcs=NPC::getAllAssigned($current_larp);
            foreach($npcs as $npc) {
                $person=$npc->getPerson();
                $registration = $person->getRegistration($current_larp);
                echo "$npc->Name: $npc->time<br>$npc->Descrption<br>"; 
                echo "Speas av $person->Name: $registration->NPCDesire<br>";
                
            }
            */
            ?>
            </div>
            
            <div>
            <h2>Alla NPC'er som inte är tilldelade</h2>
            <?php 
            /*
            $npcs=NPC::getAllUnassigned($current_larp);
            foreach($npcs as $npc) {
                echo "$npc->Name: $npc->time<br>$npc->Descrption<br>"; 
            }
            */
            ?>
            </div>
            
            <div>
            <h2>Personer som vill vara NPC</h2>
            <?php 
            $persons=Person::getAllInterestedNPC($current_larp);
            foreach($persons as $person) {
                $registration = $person->getRegistration($current_larp);
            
                echo "<a href='view_person?id=$person->Id'><strong>$person->Name</strong></a>, ";
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
  