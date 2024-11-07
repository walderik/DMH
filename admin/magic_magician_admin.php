<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Magic_Magician::delete($_GET['Id']);
    }
}


include 'navigation.php';
include 'magic_navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Magiker <a href="magic.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till magi"></i></a></h1>
        Först lägger man till en eller flera karaktärer som magiker och sedan kan man redigera deras magi-egenskaper.
        <br><br>
            <a href="choose_role.php?operation=add_magician"><i class="fa-solid fa-file-circle-plus"></i>Lägg till karaktärer som magiker</a>&nbsp;&nbsp;  
       <?php
       $magiciansComing = Magic_Magician::allByComingToLarp($current_larp);
       $allMagicians = Magic_Magician::allByCampaign($current_larp);;
       $magiciansNotComing = array_udiff($allMagicians, $magiciansComing,
           function ($objOne, $objTwo) {
               return $objOne->Id - $objTwo->Id;
           });
       

       if (!empty($magiciansComing)) {
           $personIdArr = array();
           foreach ($magiciansComing as $magician) {
               $person = $magician->getRole()->getPerson();
               $personIdArr[] = $person->Id;
           }
           echo "<h2>Magiker som är anmälda</h2>";
           
           echo contactSeveralEmailIcon('Skicka mail till alla magiker', $personIdArr, 'Magiker', "Meddelande till alla magiker på $current_larp->Name");
           
           
           $tableId = "magicians";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Grupp</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Spelas av</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Skola</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Mästare</th>".
               "<th onclick='sortTable(6, \"$tableId\")'>Stav<br>godkänd</th>".
               "<th onclick='sortTable(7, \"$tableId\")'>Workshop<br>deltagit</th>".
               "<th></th><th></th>";
           
           foreach ($magiciansComing as $magician) {
               $role = $magician->getRole();
               $person = $role->getPerson();
               $master = $magician->getMaster();
               $school = $magician->getMagicSchool();
                echo "<tr>\n";
                echo "<td><a href ='view_magician.php?id=$magician->Id'>$role->Name</a></td>\n";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo $group->getViewLink();
                echo "</td>";
                echo "<td>";
                echo "<a href='view_person.php?id=$person->Id'>$person->Name</a> ".contactEmailIcon($person);
                echo "</td>";
                echo "<td>" . $magician->Level . "</td>\n";
                echo "<td>"; 
                if (!empty($school)) echo $school->Name;
                echo "</td>\n";
                echo "<td>";
                if (isset($master)) {
                    echo "<a href ='view_magician.php?id=$master->Id'>".$master->getRole()->Name."</a>";
                }
                echo "</td>\n";
                echo "<td>" . showStatusIcon($magician->isStaffApproved()) . "</td>\n";
                echo "<td>" . showStatusIcon($magician->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='magic_magician_form.php?operation=update&Id=" . $magician->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/view_magician_logic.php?operation=delete&Id=" . $magician->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }

        if (!empty($magiciansNotComing)) {
            echo "<h2>Magiker som inte är anmälda</h2>";
            
            $tableId = "magiciansNotComing";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\")'>Grupp</th>".
                "<th onclick='sortTable(2, \"$tableId\")'>Spelas av</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>Nivå</th>".
                "<th onclick='sortTable(4, \"$tableId\")'>Skola</th>".
                "<th onclick='sortTable(5, \"$tableId\")'>Mästare</th>".
                "<th onclick='sortTable(6, \"$tableId\")'>Stav<br>godkänd</th>".
                "<th onclick='sortTable(7, \"$tableId\")'>Workshop<br>deltagit</th>".
                "<th></th><th></th>";
            
            foreach ($magiciansNotComing as $magician) {
                $role = $magician->getRole();
                $person = $role->getPerson();
                $master = $magician->getMaster();
                $school = $magician->getMagicSchool();
                echo "<tr>\n";
                echo "<td><a href ='view_magician.php?id=$magician->Id'>$role->Name</a></td>\n";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo $group->getViewLink();
                echo "</td>";
                echo "<td>";
                echo "<a href='view_person.php?id=$person->Id'>$person->Name</a> ".contactEmailIcon($person);
                echo "</td>";
                echo "<td>" . $magician->Level . "</td>\n";
                echo "<td>";
                if (!empty($school)) echo $school->Name;
                echo "</td>\n";
                echo "<td>";
                if (isset($master)) {
                    echo "<a href ='view_magician.php?id=$master->Id'>".$master->getRole()->Name."</a>";
                }
                echo "</td>\n";
                echo "<td>" . showStatusIcon($magician->isStaffApproved()) . "</td>\n";
                echo "<td>" . showStatusIcon($magician->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='magic_magician_form.php?operation=update&Id=" . $magician->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/view_magician_logic.php?operation=delete&Id=" . $magician->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        ?>
    </div>
	
</body>

</html>