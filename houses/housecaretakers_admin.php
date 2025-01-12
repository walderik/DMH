<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['house_id']) && isset($_GET['person_id']) ) {
        $housecaretaker = Housecaretaker::loadByIds($_GET['house_id'], $_GET['person_id']);
        if (empty($housecaretaker)) {
            header('Location: index.php'); // Magikern finns inte
            exit;
        }

//         if (isset($_GET['ok_person'])) { # Godkänn ett husförvaltarskap
//             $housecaretaker->IsApproved = !$housecaretaker->IsApproved;
//             $housecaretaker->update();
//             header('Location: housecaretakers_admin.php');
//             exit;
        if (isset($_GET['date'])) { # Ange när ett kontrakt skrivits under
            $housecaretaker->ContractSignedDate = $_GET['date'];
            $housecaretaker->update();
            header('Location: housecaretakers_admin.php');
            exit;
        }
    }
}

include "navigation.php";
if (isset($error_message) && strlen($error_message)>0) {
    echo '<div class="error">'.$error_message.'</div>';
}
if (isset($message_message) && strlen($message_message)>0) {
    echo '<div class="message">'.$message_message.'</div>';
}

$caretakers = Housecaretaker::all();
$resultCheck = count($caretakers);
if ($resultCheck > 0) {
    $personIdArr = array();
    foreach ($caretakers as $housecaretaker) {
        $personIdArr[] = $housecaretaker->PersonId;
    }
}

?>
<script src="../javascript/table_sort.js"></script>
<div class="content">   
    <h1>Husförvaltare 
    <?php 
    if ($resultCheck > 0) {
        echo " &nbsp; " . contactSeveralEmailIcon("", $personIdArr,
            "bäste husförvaltare!",
            "Meddelande till alla som är husförvaltare", BerghemMailer::ASSOCIATION) ;
    }
    ?>
    </h1>
    <p style='width:70%;'>
    Alla godkända husförvaltare kan redigera husbrevet och får förtur att boka huset de förvaltar.<br>
    För att lägga till en husförvaltare väljer du först vilket hus som skall få en förvaltare.<br>
    </p>
    <?php

    $tableId = "houses";
    
    if ($resultCheck > 0) {
        echo "<table id='$tableId' class='data' style='width:70%;'>";
        echo "<tr>";
        echo "<th onclick='sortTable(0, \"$tableId\");'>Namn</th>";
        echo "<th onclick='sortTable(1, \"$tableId\");'>Medlem i år</th>";
        echo "<th onclick='sortTable(2, \"$tableId\");'>Kontrakt undertecknades</th>";
//         echo "<th onclick='sortTable(3, \"$tableId\");'>Godkänd</th>";
        echo "<th onclick='sortTable(4, \"$tableId\");'>Hus</th>";
        echo "<th>Email</th>";
        echo "<th>&nbsp;</th>";
        echo "</tr>\n";
        foreach ($caretakers as $housecaretaker) {
            $person = $housecaretaker->getPerson();
            $house = $housecaretaker->getHouse();
            echo "<tr>\n";
            echo "<td>". $person->getViewLink() ."</td>\n";
            echo "<td>" . showStatusIcon($person->isMember()) . "</td>\n";
            echo "<td>";
            
            $element_id = "creationDate_$housecaretaker->PersonId"."_$housecaretaker->HouseId";
            echo "<input type='date' id='$element_id' value='". $housecaretaker->ContractSignedDate."' size='50' required>
            
            <script>
            document.getElementById('$element_id').addEventListener('change', function() {
                var selectedDate = this.value;
                var url = 'housecaretakers_admin.php?person_id=$person->Id&house_id=$house->Id&date=' + selectedDate;
                window.location.href = url;
            });
            </script>";

            if (!isset($housecaretaker->ContractSignedDate)) {
                echo showStatusIcon(false);
            }         

//             echo "<td>" . showStatusIcon($housecaretaker->IsApproved, "housecaretakers_admin.php?house_id=$housecaretaker->HouseId&person_id=$person->Id&ok_person=".time()) . "</td>\n";
            echo "<td><a href='view_house.php?operation=update&id=" . $house->Id . "'>" . $house->Name . "</a></td>\n";
            echo "  <td>".contactEmailIcon($person, BerghemMailer::ASSOCIATION)."</td>\n";
            echo "  <td>".remove_housecaretaker($person, $house)."</td>\n";
        }
        echo "</table>";
    } else {
        echo "<p>Inga husförvaltare registrerade ännu</p>";
    }
    ?>

</div>
</body>

</html>