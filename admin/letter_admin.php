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
            $letter = Letter::newFromArray($_POST);
            $letter->create();
        } elseif ($operation == 'delete') {
            Letter::delete($_POST['Id']);
        } elseif ($operation == 'update') {
            $letter=Letter::loadById($_POST['Id']);
            $letter->setValuesByArray($_POST);
            $letter->update();
        }
    }
    if (isset($_POST['Referer'])) header('Location: ' . $_POST['Referer']);
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    //     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Letter::delete($_GET['id']);
    }
}

include 'navigation.php';
?>

<script src="../javascript/table_sort.js"></script>

    <div class="content">
        <h1>Brev</h1>
        <p>Brev skapade av arrangörer blir automatiskt godkända. Brev skapade av deltagare behöver godkännas av arrangörer innan de kommer med i pdf'en.</p> 
            <a href="letter_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  &nbsp; &nbsp;
        
            <a href="logic/all_letters_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf</a>  
		<form action="letter_admin.php" method="post">
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
			

        <?php
    
        $letter_array = Letter::allBySelectedLARP($current_larp);
        if (!empty($letter_array)) {
            $tableId = "letters";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Id</td>".
            "<th onclick='sortTable(1, \"$tableId\");'>Mottagare</th>".
            "<th onclick='sortTable(2, \"$tableId\");'>Ort och datum</th>".
            "<th onclick='sortTable(3, \"$tableId\");'>Hälsning</th>".
            "<th onclick='sortTable(4, \"$tableId\");'>Meddelande</th>".
            "<th onclick='sortTable(5, \"$tableId\");'>Hälsning</th>".
            "<th onclick='sortTable(6, \"$tableId\");'>Underskrift</th>".
            "<th onclick='sortTable(7, \"$tableId\");'>Font</th>".
            "<th onclick='sortTable(8, \"$tableId\");'>Skapare</th>".
            "<th onclick='sortTable(9, \"$tableId\");'>Ok</th>".
            "<th onclick='sortTable(10, \"$tableId\");'>Anteckningar</th>".
            "<th onclick='sortTable(11, \"$tableId\");'>Används<br>i intrig</th>".
            "<th></th><th></th><th></th></tr>\n";
            foreach ($letter_array as $letter) {
                echo "<tr>\n";
                echo "<td>" . $letter->Id . "</td>\n";
                echo "<td>" . $letter->Recipient . "</td>\n";
                echo "<td>" . $letter->WhenWhere . "</td>\n";
                echo "<td>" . $letter->Greeting . "</td>\n";
                if ($short_text) {
                echo "<td>" . mb_strimwidth(str_replace("\n", "<br>", $letter->Message), 0, 100, "...") . "</td>\n";
                }
                else {
                    echo "<td>" . nl2br(htmlspecialchars($letter->Message)) . "</td>\n";
                }
                echo "<td>" . $letter->EndingPhrase . "</td>\n";
                echo "<td>" . $letter->Signature . "</td>\n";
                echo "<td>" . $letter->Font . "</td>\n";
                echo "<td>";
                $user = $letter->getUser();
                if ($user->isComing($current_larp)) {
                    echo $user->Name;
                }
                else {
                    echo "<s>$user->Name</s>";
                }
                echo "</td>\n";
                echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
                echo "<td>" . nl2br(htmlspecialchars($letter->OrganizerNotes)) . "</td>\n";
                
                echo "<td>";
                $intrigues = Intrigue::getAllIntriguesForLetter($letter->Id, $current_larp->Id);
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
                
                
                echo "<td>" . "<a href='letter_form.php?operation=update&id=" . $letter->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/show_letter.php?id=" . $letter->Id . "' target='_blank'><i class='fa-solid fa-file-pdf'></i></td>\n";
                echo "<td>";
                if (empty($intrigues)) echo "<a href='letter_admin.php?operation=delete&id=" . $letter->Id . "'><i class='fa-solid fa-trash'></i>";
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