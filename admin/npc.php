<?php
include_once 'header.php';

include 'navigation_subpage.php';
?>


    <div class="content">   
        <h1>NPC</h1>
            <a href="create_npc.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC</a>  
            <a href="create_npc_group.php"><i class="fa-solid fa-file-circle-plus"></i>Skapa NPC grupp</a>  
            
            <div>
            <?php 
            $persons=Person::getAllInterestedNPC($current_larp);
            foreach($persons as $person) {
                $registration = $person->getRegistration($current_larp);
            
                echo "$person->Name: $registration->NPCDesire<br>"; 
            }
            ?>
            </div>
  	</div>
</body>

</html>
  