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
            $rumour = Rumour::newFromArray($_POST);
            $rumour->create();
            if (isset($_POST['RoleId'])) {
                $rumour->addRoleConcerns(array($_POST['RoleId']));
            }
        } elseif ($operation == 'delete') {
            Rumour::delete($_POST['Id']);
        } elseif ($operation == 'update') {
            $rumour=Rumour::loadById($_POST['Id']);
            $rumour->setValuesByArray($_POST);
            $rumour->update();
        }
    }
    if (isset($_POST['Referer'])) header('Location: ' . $_POST['Referer']);
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Rumour::delete($_GET['id']);
    }
}

include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>


    <div class="content">
        <h1>Rykten</h1>
        <p>Rykten skapade av arrangörer blir automatiskt godkända. Rykten skapade av deltagare behöver godkännas av arrangörer innan de kan spridas.<br> 
        <a href="rumour_wizard_pg1.php">Fördela ett eller flera rykten, som ingen redan känner till, slumpmässigt <i class="fa-solid fa-wand-sparkles"></i></a><br>
        <a href="rumour_roles.php">Se hur många rykten olika karaktärer har.</a>
        </p>
		<form action="rumour_admin.php" method="post">
            <a href="rumour_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  &nbsp; &nbsp;
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
    
        $rumour_array = Rumour::allBySelectedLARP($current_larp);
        if (!empty($rumour_array)) {
            $tableId = "rumours";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Text</th>".
            "<th></th>".
            "<th onclick='sortTable(2, \"$tableId\");'>Skapare</th>".
            "<th onclick='sortTable(3, \"$tableId\");'>Gäller</th>".
            "<th onclick='sortTable(4, \"$tableId\");'>Antal som<br>känner till</th>".
            "<th onclick='sortTable(5, \"$tableId\");'>Används<br>i intrig</th>".
            "<th onclick='sortTable(6, \"$tableId\");'>Ok</th>".
            "<th></th></tr>\n";
            foreach ($rumour_array as $rumour) {
                echo "<tr>\n";
                if ($short_text) {
                    echo "<td>" . mb_strimwidth(nl2br(htmlspecialchars($rumour->Text)), 0, 100, "...") . "</td>\n";
                }
                else {
                    echo "<td>" . nl2br(htmlspecialchars($rumour->Text)) . "</td>\n";
                }
                echo "<td>" . "<a href='rumour_form.php?operation=update&id=" . $rumour->Id . "'><i class='fa-solid fa-pen' title='Ändra rykte'></i></td>\n";
                echo "<td>";
                $user = $rumour->getUser();
                if ($user->isComing($current_larp)) {
                    echo $user->Name;
                }
                else {
                    echo "<s>$user->Name</s>";
                }
                echo "</td>\n";
                echo "<td>";
                $concerns_array = $rumour->getConcerns();
                $concers_str_arr = array();
                foreach ($concerns_array as $concern) {
                    $concers_str_arr[] = $concern->getViewLink();
                }
                echo implode(", ", $concers_str_arr);
                echo "</td>";
                echo "<td>";
                echo $rumour->getKnowsCount();
                echo "</td>";
                echo "<td>";
                if (isset($rumour->IntrigueId)) {
                    $intrigue = $rumour->getIntrigue();
                    echo "<a href='view_intrigue.php?Id=$intrigue->Id'>$intrigue->Number. $intrigue->Name</a>";
                }
                echo "</td>";
                
                echo "<td>" . showStatusIcon($rumour->Approved) . "</td>\n";
                echo "<td>";
                if (!isset($rumour->IntrigueId)) echo "<a href='rumour_admin.php?operation=delete&id=" . $rumour->Id . "'><i class='fa-solid fa-trash' title='Ta bort rykte'></i>";
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