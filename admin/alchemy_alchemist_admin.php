<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Magic_Magician::delete($_GET['Id']);
    }
}


include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

 
    <div class="content">
        <h1>Alkemister <a href="magic.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till alkemi"></i></a></h1>
        Först lägger man till en eller flera karaktärer som alkemister och sedan kan man redigera deras alkemi-egenskaper.
        <br><br>
            <a href="choose_role.php?operation=add_alchemist"><i class="fa-solid fa-file-circle-plus"></i>Lägg till karaktärer som alkemister</a>  
       <?php
    
       $alchemists = Alchemy_Alchemist::allByCampaign($current_larp);
       if (!empty($alchemists)) {
           $tableId = "alchemists";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(1, \"$tableId\")'>Alkemisttyp</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Antal recept</th>".
               "<th onclick='sortTable(5, \"$tableId\")'>Workshop<br>deltagit</th>".
               "<th></th><th></th>";
           
           foreach ($alchemists as $alchemist) {
               $role = $alchemist->getRole();

                echo "<tr>\n";
                echo "<td><a href ='view_alchemist.php?id=$alchemist->Id'>$role->Name</a></td>\n";
                echo "<td>" . $alchemist->Level . "</td>\n";
                echo "<td>" . $alchemist->getAlchemistType() . "</td>\n";
                echo "<td>";
                $recipes = $alchemist->getRecipes();
                if (!empty($recipes)) echo count($recipes);
                echo "</td>\n";
                echo "<td>" . showStatusIcon($alchemist->hasDoneWorkshop()) . "</td>\n";
                echo "<td>" . "<a href='alchemy_alchemist_form.php?operation=update&Id=" . $alchemist->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/view_alchemist_logic.php?operation=delete&Id=" . $alchemist->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrerade ännu</p>";
        }
        ?>
    </div>
	
</body>

</html>