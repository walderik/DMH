<?php
 include_once 'header.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     //echo $operation;
    if ($operation == 'insert') {
        $house = House::newFromArray($_POST);
        $house->create();
    } elseif ($operation == 'delete') {
        LARP::delete($_POST['Id']);
    } elseif ($operation == 'update') {

        $house = House::newFromArray($_POST);
        $house->update();
    } else {
        echo $operation;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        House::delete($_GET['id']);
    }
}

include 'navigation_subpage.php';
?>

    <div class="content">   
        <h1>Hus i byn</h1>
            <a href="house_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $house_array = House::all();
        $resultCheck = count($house_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Antal sovplatser</th><th>Information</th><th></th><th></th></tr>\n";
            foreach ($house_array as $house) {
                echo "<tr>\n";
                echo "<td>" . $house->Id . "</td>\n";
                echo "<td>" . $house->Name . "</td>\n";
                echo "<td>" . $house->NumberOfBeds . "</td>\n";
                echo "<td>" . $house->PositionInVillage . "</td>\n";
                echo "<td>" . $house->Description . "</td>\n";
                
                echo "<td>" . "<a href='house_form.php?operation=update&id=" . $house->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='house_admin.php?operation=delete&id=" . $house->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                echo "</tr>\n";
            }
            echo "</table>";
        }
        else {
            echo "<p>Inga registrarade ännu</p>";
        }
        ?>
        
	</div>
</body>

</html>