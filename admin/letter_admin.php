<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
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

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    //     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Letter::delete($_GET['id']);
    }
}

include 'navigation_subpage.php';
?>


    <div class="content">
        <h1>Brev</h1>
        <p>Brev skapade av arrangörer blir automatiskt godkända. Brev skapade av deltagare behöver godkännas av arrangörer innan de kommer med i pdf'en.</p> 
            <a href="letter_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  &nbsp; &nbsp;
        
            <a href="logic/all_letters_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf</a>  
        
        <?php
    
        $letter_array = Letter::allBySelectedLARP($current_larp);
        if (!empty($letter_array)) {
            echo "<table id='telegrams' class='data'>";
            echo "<tr><th>Id</td><th>Ort och datum</th><th>Hälsning</th><th>Meddelande</th><th>Hälsning</th><th>Underskrift</th>";
            echo "<th>Font</th><th>Skapare</th><th>Ok</th><th>Anteckningar</th><th></th><th></th><th></th></tr>\n";
            foreach ($letter_array as $letter) {
                echo "<tr>\n";
                echo "<td>" . $letter->Id . "</td>\n";
                echo "<td>" . $letter->WhenWhere . "</td>\n";
                echo "<td>" . $letter->Greeting . "</td>\n";
                echo "<td>" . str_replace("\n", "<br>", $letter->Message) . "</td>\n";
                echo "<td>" . $letter->EndingPhrase . "</td>\n";
                echo "<td>" . $letter->Signature . "</td>\n";
                echo "<td>" . $letter->Font . "</td>\n";
                echo "<td>" . $letter->getUser()->Name . "</td>\n";
                echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
                echo "<td>" . str_replace("\n", "<br>", $letter->OrganizerNotes) . "</td>\n";
                
                echo "<td>" . "<a href='letter_form.php?operation=update&id=" . $letter->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='logic/show_letter.php?id=" . $letter->Id . "' target='_blank'><i class='fa-solid fa-file-pdf'></i></td>\n";
                echo "<td>" . "<a href='letter_admin.php?operation=delete&id=" . $letter->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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