<?php
include_once 'header.php';
include 'navigation.php';
include 'intrigue_navigation.php';

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
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Vill inte spela på</th>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Intrigidéer</th>";
            echo "<th onclick='sortTable(". $colnum++ .", \"$tableId\")'>Mörk Hemlighet / Mörk Hemlighet idéer</th>";
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
                if (!empty($player->NotAcceptableIntrigues))  echo "Spelare: ".$player->NotAcceptableIntrigues."<br><br>";
                if (!empty($character->NotAcceptableIntrigues))  echo "Karaktär: ".$character->NotAcceptableIntrigues;
                echo "</td>\n";

                echo "<td>";
                $larp_role = LARP_Role::loadByIds($character->Id, $current_larp->Id);
                echo $larp_role->IntrigueIdeas;
                echo "</td>\n";

                echo "<td>";
                echo $character->DarkSecret;
                echo "<br><br>";
                echo $character->DarkSecretIntrigueIdeas;
                echo "</td>\n";

                echo "<td>";

                echo "<textarea id='OrganizerNotes:$character->Id' name='OrganizerNotes' rows='5' cols='50' maxlength='60000' onkeyup='saveNotesForRole(this)' onchange='saveNotesForRole(this)'>" . htmlspecialchars($character->OrganizerNotes) . "</textarea><br>";
                echo "</td>";
                
                echo "</tr>";
                }
            
                echo "</table>";
            }
            ?>
        </div>
        <script src="../javascript/saveRoleNotes_ajax.js"></script>
    </body>
</html>