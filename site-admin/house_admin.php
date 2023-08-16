<?php
 include_once 'header.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $house = House::newFromArray($_POST);
        $house->create();
    } elseif ($operation == 'delete') {
        LARP::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $house=House::loadById($_POST['Id']);
        $house->setValuesByArray($_POST);
        $house->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        House::delete($_GET['id']);
    }
}

include "navigation.php";
?>

    <div class="content">   
        <h1>Hus och lägerplatser</h1>
            <a href="house_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $house_array = House::all();
        $resultCheck = count($house_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Typ</th><th>Antal sovplatser/<br>tältplatser</th><th>Plats</th><th>Beskrivning</th><th></th><th></th><th></th></tr>\n";
            foreach ($house_array as $house) {
                echo "<tr>\n";
                echo "<td>" . $house->Id . "</td>\n";
                echo "<td>" . $house->Name . "</td>\n";
                echo "<td>";
                if ($house->IsHouse()) echo "Hus";
                else echo "Lägerplats";
                echo "</td>";
                echo "<td>" . $house->NumberOfBeds . "</td>\n";
                echo "<td>" . $house->PositionInVillage . "</td>\n";
                echo "<td>" . $house->Description . "</td>\n";
                
                if ($house->hasImage()) {
                    $image = Image::loadById($house->ImageId);
                    echo "<td><img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/> <a href='logic/delete_image.php?id=$house->Id&type=house'>Ta bort bild</a></td>\n";
                }
                else {
                    echo "<td><a href='upload_image.php?id=$house->Id&type=house'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
                }
                
                
                echo "<td>" . "<a href='house_form.php?operation=update&id=" . $house->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='house_admin.php?operation=delete&id=" . $house->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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