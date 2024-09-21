<?php
 include_once 'header.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $house = House::newFromArray($_POST);
        $house->create();
    } elseif ($operation == 'update') {
        $house=House::loadById($_POST['Id']);
        $house->setValuesByArray($_POST);
        $house->update();
    }
    header('Location: house_admin.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {

        $house=House::loadById($_GET['id']);
        if ($house->mayDelete()) {
            $caretakers = $house->getHousecaretakers();
            foreach ($caretakers as $caretaker) {
                $caretaker->destroy();
            }
            if ($house->hasImage()) {
                $image = $house->getImage();
                $image->destroy();
            }
            
            if ($house->hasHousing()) {
                
            } else {
                $house->destroy();
            }
        }
        
        header('Location: house_admin.php');
        exit;
    }
}

include "navigation.php";
?>

<div class="content">   
    <h1>Hus och lägerplatser</h1>
    <a href='all_houses.php'><i class='fa fa-map' aria-hidden='true'></i>Karta med alla hus</a><br> 
    <a href='all_housing.php'><i class='fa fa-map' aria-hidden='true'></i>Karta med alla hus och lägerplatser</a>
    <p><a href="house_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i> Lägg till</a></p>  <br>
    
    <?php
        
        $house_array = House::all();
        $resultCheck = count($house_array);
        if ($resultCheck > 0) {
            echo "<table id='houses' class='data'>";
            echo "<tr><th>Namn</th><th>Typ</th><th>Sovplatser/<br>Tältplatser</th><th>Plats</th><th>Beskrivning</th><th>Förvaltare</th><th></th><th></th><th></th></tr>\n";
            foreach ($house_array as $house) {
                
                echo "<tr>\n";
                echo "<td><a href='view_house.php?operation=update&id=" . $house->Id . "'>" . $house->Name . "</a></td>\n";
                echo "<td>";
                if ($house->IsHouse()) echo "Hus";
                else echo "Lägerplats";
                echo "</td>";
                echo "<td width='8%'>" . $house->NumberOfBeds . "</td>\n";
                echo "<td width='20%' style='word-break: break-all';>" . $house->PositionInVillage . "</td>\n";
                echo "<td>" . $house->Description . "</td>\n";
                
                echo "<td nowrap>";
                
                $caretakers = $house->getHousecaretakers();
                foreach ($caretakers as $house_caretaker) {
                    $person = $house_caretaker->getPerson();
                    echo "$person->Name ";
                    if (!$house_caretaker->isMember()) {
                        echo '<img src="../images/alert-icon.png" alt="Inte medlem i Berghems vänner i år" title="Inte medlem i Berghems vänner i år" width="20" height="20">';
                    }
                    echo "<br>";
                }
                echo "</td>";
                
                if ($house->hasImage()) {
                    $image = $house->getImage();
                    $photografer = (!empty($image->Photographer) && $image->Photographer!="") ? "Fotograf $image->Photographer" : "";
                    echo "<td><img width=45 src='../includes/display_image.php?id=$house->ImageId' title='$photografer'/><br>\n";
                } else {
                    echo "<td><a href='upload_image.php?id=$house->Id&type=house'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
                }
                
                echo "<td><a href='house_form.php?operation=update&id=$house->Id' title=\"Redigera beskrivningen\"><i class='fa-solid fa-pen'></i></td>\n";
                
                $txt = '"Är du säker att du helt vill ta bort '.$house->Name.'?"';
                $confirm = "onclick='return confirm($txt)'";
                echo "<td>";
                if ($house->mayDelete()) echo "<a href='house_admin.php?operation=delete&id=$house->Id' $confirm><i class='fa-solid fa-trash'></i>";
                echo "</td>\n";
                
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