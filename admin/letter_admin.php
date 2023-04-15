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
        
            <a href="letter_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf</a>  
        
        <?php
    
        $letter_array = Letter::allBySelectedLARP($current_larp);
        if (!empty($letter_array)) {
            echo "<table id='telegrams' class='data'>";
            echo "<tr><th>Id</td><th>Leveranstid</th><th>Avsändare</th><th>Avsändarens stad</th><th>Mottagare</th>";
            echo "<th>Mottagarens stad</th><th>Meddelande</th><th>Font</th><th>Skapare</th><th>Ok</th><th>Anteckningar</th><th></th><th></th></tr>\n";
            foreach ($letter_array as $letter) {
                echo "<tr>\n";
                echo "<td>" . $letter->Id . "</td>\n";
                echo "<td>" . $letter->Deliverytime . "</td>\n";
                echo "<td>" . $letter->Sender . "</td>\n";
                echo "<td>" . $letter->SenderCity . "</td>\n";
                echo "<td>" . $letter->Reciever . "</td>\n";
                echo "<td>" . $letter->RecieverCity . "</td>\n";
                echo "<td>" . str_replace("\n", "<br>", $letter->Message) . "</td>\n";
                echo "<td>" . $letter->Font . "</td>\n";
                echo "<td>" . $letter->getUser()->Name . "</td>\n";
                echo "<td>" . showStatusIcon($letter->Approved) . "</td>\n";
                echo "<td>" . str_replace("\n", "<br>", $letter->OrganizerNotes) . "</td>\n";
                
                echo "<td>" . "<a href='letter_form.php?operation=update&id=" . $letter->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
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