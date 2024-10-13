<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    //     $operation = $_GET['operation'];
    if (isset($_GET['ok_house']) && isset($_GET['ok_person'])) {
        
        $house_caretaker = Housecaretaker::loadByIds($_GET['ok_house'], $_GET['ok_person']);
        if (empty($house_caretaker)) {
            header('Location: index.php'); // Magikern finns inte
            exit;
        }
        $house_caretaker->IsApproved = !$house_caretaker->IsApproved;
        $house_caretaker->update();
        header('Location: housecaretakers_admin.php');
        exit;
    }
    $type = "house";
    if (isset($_GET['type'])) $type = $_GET['type'];
}

include "navigation.php";

$caretakers_array = Housecaretaker::all();

?>
<script src="../javascript/table_sort.js"></script>
<div class="content">   
    <h1>Husförvaltare</h1>
    <?php

    $tableId = "houses";
    $resultCheck = count($caretakers_array);
    if ($resultCheck > 0) {
        echo "<table id='$tableId' class='data'>";
        echo "<tr>";
        echo "<th onclick='sortTable(0, \"$tableId\");'>Namn</th>";
        echo "<th onclick='sortTable(1, \"$tableId\");'>Medlem i år</th>";
        echo "<th onclick='sortTable(2, \"$tableId\");'>Kontrakt undertecknades</th>";
        echo "<th onclick='sortTable(3, \"$tableId\");'>Godkänd</th>";
        echo "<th onclick='sortTable(4, \"$tableId\");'>Hus</th>";
        echo "</tr>\n";
        foreach ($caretakers_array as $house_caretaker) {
            $person = $house_caretaker->getPerson();
            $house = $house_caretaker->getHouse();
            echo "<tr>\n";
            echo "<td>$person->Name </td>\n";
            echo "<td>" . showStatusIcon($person->isMember()) . "</td>\n";
            if (isset($house_caretaker->ContractSignedDate)) {
                echo "<td>".$house_caretaker->ContractSignedDate.  "</td>\n";
            } else {
                echo "<td>" . showStatusIcon(false) . "</td>\n";
            }
            echo "<td>" . showStatusIcon($house_caretaker->IsApproved, "housecaretakers_admin.php?ok_house=$house_caretaker->HouseId&ok_person=$house_caretaker->PersonId") . "</td>\n";
            echo "<td><a href='view_house.php?operation=update&id=" . $house->Id . "'>" . $house->Name . "</a></td>\n";
        }
        echo "</table>";
    } else {
        echo "<p>Inga husförvaltare registrerade ännu</p>";
    }
    ?>

</div>
</body>

</html>