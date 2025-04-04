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
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Anteckningar</th>";
            
            foreach ($characters as $character) {
                echo "<tr>";
                echo "<td>";
                echo $character->getViewLink();
                $player = $character->getPerson();
                echo "<br>";
                echo "Spelas av: ";
                if (!is_null($player)) echo $player->getViewLink();
                else echo "NPC";

                $group = $character->getGroup();
                if (!is_null($group)){
                    echo "<br>";
                    echo "Grupp: ";
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

                $referer = '../intrigue_creator_page.php';
                echo "<td>";
                echo "<form action='logic/edit_intrigue_save.php' method='post'>";
                echo "<input type='hidden' id='Id' name='Id' value='" . $character->Id . "'>";
                echo "<input type='hidden' id='Referer' name='Referer' value='" . htmlspecialchars($referer) . "'>";
                echo "<textarea id='OrganizerNotes' name='OrganizerNotes' rows='5' cols='50' maxlength='60000'>" . htmlspecialchars($character->OrganizerNotes) . "</textarea><br>";
                echo "<input type='submit' value='Spara'>";
                echo "</form>";
                echo "</td>";
                
                echo "</tr>";
                }
            
                echo "</table>";
            }
            ?>
        </div>
    </body>
</html>