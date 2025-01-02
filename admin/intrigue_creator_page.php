<?php
include_once 'header.php';
include 'navigation.php';
?>

<style>
th {
  cursor: pointer;
}
</style>

<body>
    <script src="../javascript/table_sort.js"></script>

        <div class="content">   
            <h1>Intrigskapare</h1>

            <?php 
            $characters = $current_larp->getAllMainRoles(false);

            if (empty($characters)) {
                echo "Inga anmälda karaktärer";
            } else {
            $tableId = "characters";
            $colnum = 0;

            echo "<table id='$tableId' class='data'>";
            echo "<tr>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Karaktär</th>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intrigidéer</th>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Mörk Hemlighet</th>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Mörk Hemlighet idéer</th>";
            // echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Anteckningar</th>";
            
            foreach ($characters as $character) {
                echo "<tr>";
                echo "<td>";
                echo $character->getViewLink();
                $group = $character->getGroup();
                if (!is_null($group)){
                    echo "<br>";
                    echo $group->getViewLink();
                }
                echo "</td\n>";

                echo "<td>";
                echo $character->IntrigueSuggestions;
                echo "</td>\n";

                echo "<td>";
                echo $character->DarkSecret;
                echo "</td>\n";

                echo "<td>";
                echo $character->DarkSecretIntrigueIdeas;
                echo "</td>\n";
                
                echo "</tr>";

                //TODO
                // echo "<td>";
                // echo $character->OrganizerNotes;
                // echo "</td>\n";
                }
            
                echo "</table>";
            }
            ?>
        </div>
    </body>
</html>