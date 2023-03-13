<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     //echo $operation;
    if ($operation == 'insert') {
        $prop = Prop::newFromArray($_POST);
        $prop->create();
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $prop = Prop::newFromArray($_POST);
        $prop->update();
    } else {
        echo $operation;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Prop::delete($_GET['id']);
    }
}

$owner = "";
if (isset($prop->GroupId)) {
    $group = Group::loadById($prop->GroupId);
    $owner = $group->Name;
}
elseif (isset($prop->RoleId)) {
    $role = Role::loadById($prop->RoleId);
    $owner = $role->Name;
}
include 'navigation_subpage.php';
?>

    <div class="content">
        <h1>Rekvisita</h1>
            <a href="prop_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
       <?php
    
       $prop_array = Prop::allByCampaign();
        if (!empty($prop_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Beskrivning</th><th>Lagerplats</th><th>Innehavare</th><th></th><th></th></tr>\n";
            foreach ($prop_array as $prop) {
                echo "<tr>\n";
                echo "<td>" . $prop->Id . "</td>\n";
                echo "<td>" . $prop->Name . "</td>\n";
                echo "<td>" . $prop->Description . "</td>\n";
                echo "<td>" . $prop->StorageLocation . "</td>\n";
                echo "<td>" . $owner . "</td>\n";


                
                echo "<td>" . "<a href='prop_form.php?operation=update&id=" . $prop->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='prop_admin.php?operation=delete&id=" . $prop->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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