<?php
 include_once 'header.php';
 include_once '../includes/selection_data_control.php';
 
 $type;
 $objectType;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['type'])) {
        $type=$_POST['type'];
    }
    else {
        header('Location: index.php');
        exit;
        
    }
    $objectType = getObjectType($type);

    $operation = $_POST['operation'];



    if ($operation == 'insert') {
        $user_func = $objectType . '::newFromArray';
        $data = call_user_func($user_func, $_POST);

        $data->create();
    } elseif ($operation == 'update') {
        $user_func = $objectType . '::loadById';
        $data = call_user_func($user_func,$_POST['Id']);
        $data->setValuesByArray($_POST);
        $data->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['type'])) {
        $type=$_GET['type'];
    }
    else {
        header('Location: index.php');
        exit;
        
    }
    $objectType = getObjectType($type);
}

include "navigation.php";

?>
    <div class="content">   
        <h1><?php echo getObjectName($type);?></h1>
            <a href="selection_data_general_form.php?type=<?php echo $type;?>&operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>          
        <?php
        $data_array = call_user_func($objectType . '::all');
        if (count($data_array) > 0) {
            echo "<table class='data'>";
            echo "<tr><th>Id</th><th>Namn</th><th>Beskrivning</th><th>Valbar</th><th>Sortering</th><th></th></tr>\n";
            foreach ($data_array as $data) {
                echo "<tr>\n";
                echo "<td>" . $data->Id . "</td>\n";
                echo "<td>" . $data->Name . "</td>\n";
                echo "<td>" . $data->Description . "</td>\n";
                echo "<td>" . $data->Active . "</td>\n";
                echo "<td>" . $data->SortOrder . "</td>\n";
              
                echo "<td>" . "<a href='selection_data_general_form.php?type=".$type."&operation=update&id=" . $data->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
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