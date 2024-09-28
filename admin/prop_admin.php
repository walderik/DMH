<?php
include_once 'header.php';





if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Prop::delete($_GET['id']);
    }
}


include 'navigation.php';
?>

    <div class="content">
        <h1>Rekvisita</h1>
            <a href="prop_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>&nbsp;
        <a href="logic/props_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i> Generera pdf</a><br>
        
       <?php
    
       $prop_array = Prop::allByCampaign($current_larp);
        if (!empty($prop_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Beskrivning</th><th>Lagerplats</th><th>Märkning</th><th>In-lajv<br>egenskaper</th><th>Innehavare</th><th>Bild</th><th>Används<br>i intrig</th><th></th></tr>\n";
            foreach ($prop_array as $prop) {
                $owner = "";
                if (isset($prop->GroupId)) {
                    $group = Group::loadById($prop->GroupId);
                    $owner = $group->Name;
                }
                elseif (isset($prop->RoleId)) {
                    $role = Role::loadById($prop->RoleId);
                    $owner = $role->Name;
                }
                echo "<tr>\n";
                echo "<td>" . $prop->Id . "</td>\n";
                echo "<td><a href='prop_form.php?operation=update&id=$prop->Id'>$prop->Name</a></td>\n";
                echo "<td>" . $prop->Description . "</td>\n";
                echo "<td>" . $prop->StorageLocation . "</td>\n";
                echo "<td>" . $prop->Marking . "</td>\n";
                echo "<td>" . $prop->Properties . "</td>\n";
                echo "<td>" . $owner . "<a href='prop_owner_form.php?id=" . $prop->Id . "'><i class='fa-solid fa-pen'></i></td>\n";

                if ($prop->hasImage()) {
                    echo "<td><img width='30' src='../includes/display_image.php?id=$prop->ImageId'/>\n";
                    echo " <a href='../common/logic/rotate_image.php?id=$prop->ImageId'><i class='fa-solid fa-rotate-right'></i></a> <a href='logic/delete_image.php?id=$prop->Id&type=prop'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a></td>\n";
                }
                else {
                    echo "<td><a href='upload_image.php?id=$prop->Id&type=prop'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
                }
                
                echo "<td>";
                $intrigues = Intrigue::getAllIntriguesForProp($prop->Id, $current_larp->Id);
                echo "<br>";
                if (!empty($intrigues)) echo "Intrig: ";
                foreach ($intrigues as $intrigue) {
                    echo "<a href='view_intrigue.php?Id=$intrigue->Id'>";
                    if ($intrigue->isActive()) echo $intrigue->Number;
                    else echo "<s>$intrigue->Number</s>";
                    echo "</a>";
                    echo " ";
                }
                echo "</td>";
                
                
                echo "<td>";
                if (empty($intrigues)) echo "<a href='prop_admin.php?operation=delete&id=" . $prop->Id . "'><i class='fa-solid fa-trash'></i>";
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