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
        
        header('Location: housecaretakers_admin.php');
        exit;
    }
    $type = "house";
    if (isset($_GET['type'])) $type = $_GET['type'];
}

include "navigation.php";

$caretakers_array = Housecaretaker::getAll();



?>

<div class="content">   
    <h1>Husförvaltare</h1>
    <?php

    $resultCheck = count($caretakers_array);
        if ($resultCheck > 0) {
            echo "<table id='houses' class='data'>";
            echo "<tr><th>Namn</th>";
            echo "<th>Medlem i år</th><th>Kontrakt undertecknades</th><th>Godkänd</th><th>Hus</th></tr>\n";
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
        }
        else {
            echo "<p>Inga husförvaltare registrerade ännu</p>";
        }
        ?>
    
</div>
</body>

</html>