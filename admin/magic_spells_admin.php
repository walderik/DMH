<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Magic_Spell::delete($_GET['Id']);
    }
}

include 'navigation.php';
include 'magic_navigation.php';
?>
<script src="../javascript/table_sort.js"></script>

    <div class="content">
        <h1>Magier <a href="magic.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till magi"></i></a></h1>
            <a href="magic_spell_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
       <?php
    
       $spells = Magic_Spell::allByCampaign($current_larp);
       if (!empty($spells)) {
           $tableId = "spells";
           echo "<table id='$tableId' class='data'>";
           echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Namn</th>".
               "<th onclick='sortTableNumbers(1, \"$tableId\")'>Nivå</th>".
               "<th onclick='sortTable(2, \"$tableId\")'>Beskrivning</th>".
               "<th onclick='sortTable(3, \"$tableId\")'>Magisort</th>".
               "<th></th><th></th>";
            foreach ($spells as $spell) {
                echo "<tr>\n";
                echo "<td><a href='view_magicspell.php?id=$spell->Id'>$spell->Name</a></td>\n";
                echo "<td>" . $spell->Level . "</td>\n";
                echo "<td>" . $spell->Description . "</td>\n";
                echo "<td>" . Magic_Spell::TYPES[$spell->Type] . "</td>\n";
                
                echo "<td>" . "<a href='magic_spell_form.php?operation=update&Id=" . $spell->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='magic_spell_form.php?operation=delete&Id=" . $spell->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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