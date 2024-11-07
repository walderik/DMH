<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Magic_Magician::delete($_GET['Id']);
    }
}


include 'navigation.php';
include 'alchemy_navigation.php';
?>

<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Alkemister <a href="alchemy.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>
        Först lägger man till en eller flera karaktärer som alkemister och sedan kan man redigera deras alkemi-egenskaper.
        <br><br>
            <a href="choose_role.php?operation=add_alchemist"><i class="fa-solid fa-file-circle-plus"></i>Lägg till karaktärer som alkemister</a>&nbsp;&nbsp; 
       <?php
       $alchemistsComing = Alchemy_Alchemist::allByComingToLarp($current_larp);
       $allAlchemists = Alchemy_Alchemist::allByCampaign($current_larp);;
       $alchemistsNotComing = array_udiff($allAlchemists, $alchemistsComing,
           function ($objOne, $objTwo) {
               return $objOne->Id - $objTwo->Id;
           });

       if (!empty($alchemistsComing)) {
           $personIdArr = array();
           foreach ($alchemistsComing as $alchemist) {
               $person = $alchemist->getRole()->getPerson();
               $personIdArr[] = $person->Id;
           }
           echo "<h2>Alkemister som är anmälda</h2>";
           echo contactSeveralEmailIcon('Skicka mail till alla anmälda alkemister', $personIdArr, 'Alkemist', "Meddelande till alla alkemister i $current_larp->Name");
           
           echo "<br>";
           
           $tableId = "alchemists";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Grupp</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Spelas av</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(4, \"$tableId\")'>Alkemisttyp</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Antal recept<br>Godkänd / Icke godkänd</th>".
               "<th onclick='sortTable(6, \"$tableId\")'>Workshop<br>deltagit</th>".
               "<th></th><th></th>";
           
           foreach ($alchemistsComing as $alchemist) {
               $role = $alchemist->getRole();
               $person = $role->getPerson();
                echo "<tr>\n";
                echo "<td><a href ='view_alchemist.php?id=$alchemist->Id'>$role->Name</a></td>\n";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo $group->getViewLink();
                echo "</td>";
                echo "<td>";
                echo "<a href='view_person.php?id=$person->Id'>$person->Name</a> (".$person->getAgeAtLarp($current_larp)." år)".contactEmailIcon($person);
                echo "</td>";
                echo "<td>" . $alchemist->Level . "</td>\n";
                echo "<td>" . $alchemist->getAlchemistType() . "</td>\n";
                echo "<td>";
                $recipes = $alchemist->getRecipes(false);
                if (!empty($recipes)) {
                    echo count($recipes)." ";
                    echo showStatusIcon($alchemist->recipeListApproved());
                }
                echo "</td>\n";
                echo "<td>" . showStatusIcon($alchemist->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_alchemist_form.php?operation=update&Id=" . $alchemist->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/view_alchemist_logic.php?operation=delete&id=" . $alchemist->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        
        if (!empty($alchemistsNotComing)) {
            
            echo "<h2>Alkemister som inte är anmälda</h2>";
            $tableId = "alchemistsNotComing";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
                "<th onclick='sortTable(1, \"$tableId\")'>Grupp</th>".
                "<th onclick='sortTable(2, \"$tableId\")'>Spelas av</th>".
                "<th onclick='sortTable(3, \"$tableId\")'>Nivå</th>".
                "<th onclick='sortTable(4, \"$tableId\")'>Alkemisttyp</th>".
                "<th onclick='sortTable(5, \"$tableId\")'>Antal recept<br>Godkänd / Icke godkänd</th>".
                "<th onclick='sortTable(6, \"$tableId\")'>Workshop<br>deltagit</th>".
                "<th></th><th></th>";
            
            foreach ($alchemistsNotComing as $alchemist) {
                $role = $alchemist->getRole();
                $person = $role->getPerson();
                echo "<tr>\n";
                echo "<td><a href ='view_alchemist.php?id=$alchemist->Id'>$role->Name</a></td>\n";
                echo "<td>";
                $group = $role->getGroup();
                if (isset($group)) echo $group->getViewLink();
                echo "</td>";
                echo "<td>";
                echo "<a href='view_person.php?id=$person->Id'>$person->Name</a> (".$person->getAgeAtLarp($current_larp)." år)".contactEmailIcon($person);
                echo "</td>";
                echo "<td>" . $alchemist->Level . "</td>\n";
                echo "<td>" . $alchemist->getAlchemistType() . "</td>\n";
                echo "<td>";
                $recipes = $alchemist->getRecipes(false);
                if (!empty($recipes)) {
                    echo count($recipes)." ";
                    echo showStatusIcon($alchemist->recipeListApproved());
                }
                echo "</td>\n";
                echo "<td>" . showStatusIcon($alchemist->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_alchemist_form.php?operation=update&Id=" . $alchemist->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/view_alchemist_logic.php?operation=delete&id=" . $alchemist->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
         
        ?>
    </div>
	
</body>

</html>