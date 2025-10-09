<?php
include_once 'header.php';

$persons=Person::getAllInterestedNPC($current_larp);



include 'navigation.php';
include 'npc_navigation.php';
?>



<script src="../javascript/table_sort.js"></script>

    <div class="content">   

          
            <h1>Deltagare som vill spela NPC</h1>
            <?php 
            $tableId = "npc_participants";
            $colnum = 0;
            echo "<table id='$tableId' class='data'>";
            echo "<tr>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\");'>Namn</th>";
            
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Karaktär(er)</th>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Önskemål</th>";
            echo "</tr>";
            
            foreach($persons as $person) {
                echo "<tr>";
                $registration = $person->getRegistration($current_larp);
            
                echo "<td>".$person->getViewLink();
                if (LarperType::isInUse($current_larp)) echo $person->getMainRole($current_larp)->getLarperType()->Name."<br>";
                echo "</td>";

                echo "<td>";
                $roles = Role::getRegistredRolesForPerson($person, $current_larp);
                $first = true;
                foreach ($roles as $role) {
                    if ($first) $first = false;
                    else echo "<br>";
                    echo $role->getViewLink();
                    $group = $role->getGroup();
                    if (!empty($group)) echo " (".$group->getViewLink().")";
                }
                echo "</td>";
                
                echo "<td> $registration->NPCDesire</td>";
            }
            ?>

  	</div>
</body>

</html>
  