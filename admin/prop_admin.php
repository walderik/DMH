<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $prop = Prop::newFromArray($_POST);
        $prop->create();
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $prop=Prop::loadById($_POST['Id']);
        $prop->setValuesByArray($_POST);
        $prop->update();
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
    
       $prop_array = Prop::allByCampaign($current_larp);
        if (!empty($prop_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Beskrivning</th><th>Lagerplats</th><th>Märkning</th><th>In-lajv<br>egenskaper</th><th>Innehavare</th><th>Bild</th><th></th><th></th></tr>\n";
            foreach ($prop_array as $prop) {
                echo "<tr>\n";
                echo "<td>" . $prop->Id . "</td>\n";
                echo "<td>" . $prop->Name . "</td>\n";
                echo "<td>" . $prop->Description . "</td>\n";
                echo "<td>" . $prop->StorageLocation . "</td>\n";
                echo "<td>" . $prop->Marking . "</td>\n";
                echo "<td>" . $prop->Properties . "</td>\n";
                echo "<td>" . $owner . "</td>\n";

                if ($prop->hasImage()) {
                    $image = Image::loadById($prop->ImageId);
                    echo "<td><img width=30 src='data:image/jpeg;base64,".base64_encode($image->file_data)."'/> <a href='logic/delete_image.php?id=$prop->Id&type=prop'>Ta bort bild</a></td>\n";
                }
                else {
                    echo "<td><a href='upload_image.php?id=$prop->Id&type=prop'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
                }
                
                
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