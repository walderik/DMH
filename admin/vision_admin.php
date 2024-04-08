<?php
include_once 'header.php';

global $short_text;

$short_text = true;

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['long_text'])) {
     $short_text = false;   
    }
    
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];

    
        if ($operation == 'insert') {
            print_r($_POST);
            $vision = Vision::newFromArray($_POST);
            $vision->create();
        } elseif ($operation == 'delete') {
            Vision::delete($_POST['Id']);
        } elseif ($operation == 'update') {
            $vision=Vision::loadById($_POST['Id']);
            $vision->setValuesByArray($_POST);
            $vision->update();
        } elseif ($operation == 'add_has_role_admin') {
            $vision=Vision::loadById($_POST['Id']);
            if (isset($_POST['RoleId'])) $vision->addRolesHas($_POST['RoleId']);
        }
    }
    if (isset($_POST['Referer'])) header('Location: ' . $_POST['Referer']);
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['operation'])) {
        $operation = $_GET['operation'];
        if ($operation == 'delete') {
            Vision::delete($_GET['id']);
        }  elseif ($operation == 'delete_has') {
            $vision=Vision::loadById($_GET['id']);
            $vision->removeRoleHas($_GET['hasId']);
        }
        header('Location: vision_admin.php');
        exit;
    }    
}

include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>


    <div class="content">
        <h1>Syner</h1>
        <!--
        <p>
          <a href="vision_wizard_pg1.php">Fördela en eller flera syner, som ingen redan kommer att ha, slumpmässigt <i class="fa-solid fa-wand-sparkles"></i></a>
        </p>
         -->
		<div class='linklist'>
            <a href="reports/visions_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf med alla syner, för deltagare</a> <br> 
            <a href="reports/vision_sheet.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf när var och en ska ha sina syner</a><br> 
            <a href="reports/all_visions_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf med alla syner, för arrangör</a>
            </div>
            <a href="vision_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  &nbsp; &nbsp;
		<form action="vision_admin.php" method="post">            
		<?php
		if ($short_text) {
		    echo "<input type='hidden' id='long_text' name='long_text' value='1'>";
		    echo "<input id='submit_button' type='submit' value='Visa full text'>";
		}
		else {
		    echo "<input id='submit_button' type='submit' value='Visa förkortad text'>";
		}
		?>
		</form>
		<br>	

        <?php
    
        $vision_array = Vision::allBySelectedLARP($current_larp);
        if (!empty($vision_array)) {
            $tableId = "visions";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>När</th>".
            "<th onclick='sortTable(1, \"$tableId\");'>Syn</th>".
            "<th></th>".
            "<th onclick='sortTable(3, \"$tableId\");'>Källa</th>".
            "<th onclick='sortTable(4, \"$tableId\");'>Bieffekt</th>".
            "<th onclick='sortTable(5, \"$tableId\");'>Vilka<br>som har</th>".
            "<th onclick='sortTable(6, \"$tableId\");'>Används<br>i intrig</th>".

            "<th></th></tr>\n";
            foreach ($vision_array as $vision) {
                echo "<tr>\n";
                echo "<td>$vision->WhenDate, " . $vision->getTimeOfDayStr() . "</td>\n";

                if ($short_text) {
                    echo "<td>" . mb_strimwidth(nl2br(htmlspecialchars($vision->VisionText)), 0, 100, "...") . "</td>\n";
                }
                else {
                    echo "<td>" . nl2br(htmlspecialchars($vision->VisionText)) . "</td>\n";
                }
                echo "<td>" . "<a href='vision_form.php?operation=update&id=" . $vision->Id . "'><i class='fa-solid fa-pen' title='Ändra syn'></i></td>\n";
                echo "<td>" . nl2br(htmlspecialchars($vision->Source)) . "</td>\n";
                echo "</td>\n";
                echo "<td>" . nl2br(htmlspecialchars($vision->SideEffect)) . "</td>\n";
                echo "</td>\n";
                echo "<td>";
                echo "<a href='choose_role.php?operation=add_has_role_admin&id=$vision->Id'><i class='fa-solid fa-plus' title='Lägg till karaktär'></i><i class='fa-solid fa-user' title='Lägg till karaktär'></i></a><br>";
                
                $has_roles = $vision->getHas();
                foreach ($has_roles as $has_role) {
                    echo "<a href='view_role.php?id=$has_role->Id'>$has_role->Name</a>  ";
                    echo "<a href='vision_admin.php?operation=delete_has&id=$vision->Id&hasId=$has_role->Id'><i class='fa-solid fa-trash'></i></a><br>";
                    
                }
                echo "</td>\n";
                echo "<td>";
                $intrigues = $vision->getAllIntriguesForVision();
                if (!empty($intrigues)) {
                    foreach ($intrigues as $intrigue) {
                        echo "<a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a><br>";
                    }
                }
                echo "</td>";
                
                echo "<td>";
                if ($vision->mayDelete()) 
                    echo "<a href='vision_admin.php?operation=delete&id=$vision->Id'><i class='fa-solid fa-trash' title='Ta bort syn'></i>";
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