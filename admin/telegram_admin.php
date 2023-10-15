<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $telegram = Telegram::newFromArray($_POST);
        $telegram->create();
    } elseif ($operation == 'delete') {
        Telegram::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $telegram=Telegram::loadById($_POST['Id']);
        $telegram->setValuesByArray($_POST);
        $telegram->update();
    } 
    if (isset($_POST['Referer'])) header('Location: ' . $_POST['Referer']);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Telegram::delete($_GET['id']);
    }
}

include 'navigation.php';
?>
<script src="../javascript/table_sort.js"></script>


    <div class="content">
        <h1>Telegram</h1>
        <p>Telegram skapade av arrangörer blir automatiskt godkända. Telegram skapade av deltagare behöver godkännas av arrangörer innan de kommer med i pdf:en.</p> 
            <a href="telegram_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  &nbsp; &nbsp;
        
            <a href="logic/all_telegrams_pdf.php" target="_blank"><i class="fa-solid fa-file-pdf"></i>Generera pdf</a>  
        
        <?php
    
        $telegram_array = Telegram::allBySelectedLARP($current_larp);
        if (!empty($telegram_array)) {
            $tableId = "telegrams";
            echo "<table id='$tableId' class='data'>";
            echo "<tr><th onclick='sortTable(0, \"$tableId\");'>Id</td>".
            "<th onclick='sortTable(1, \"$tableId\");'>Leveranstid</th>".
            "<th onclick='sortTable(2, \"$tableId\");'>Avsändare</th>".
            "<th onclick='sortTable(3, \"$tableId\");'>Avsändarens stad</th>".
            "<th onclick='sortTable(4, \"$tableId\");'>Mottagare</th>".
            "<th onclick='sortTable(5, \"$tableId\");'>Mottagarens stad</th>".
            "<th onclick='sortTable(6, \"$tableId\");'>Meddelande</th>".
            "<th onclick='sortTable(7, \"$tableId\");'>Skapare</th>".
            "<th onclick='sortTable(8, \"$tableId\");'>Ok</th>".
            "<th onclick='sortTable(9, \"$tableId\");'>Anteckningar</th>".
            "<th onclick='sortTable(10, \"$tableId\");'>Används<br>i intrig</th>".
            "<th></th><th></th></tr>\n";
            foreach ($telegram_array as $telegram) {
                echo "<tr>\n";
                echo "<td>" . $telegram->Id . "</td>\n";
                echo "<td>" . $telegram->Deliverytime . "</td>\n";
                echo "<td>" . $telegram->Sender . "</td>\n";
                echo "<td>" . $telegram->SenderCity . "</td>\n";
                echo "<td>" . $telegram->Reciever . "</td>\n";
                echo "<td>" . $telegram->RecieverCity . "</td>\n";
                echo "<td>" . nl2br(htmlspecialchars($telegram->Message)) . "</td>\n";
                echo "<td>";
                $user = $telegram->getUser();
                if ($user->isComing($current_larp)) {
                    echo $user->Name;
                }
                else {
                    echo "<s>$user->Name</s>";
                }
                echo "</td>\n";
                echo "<td>" . showStatusIcon($telegram->Approved,  "logic/approve_telegram.php?id=$telegram->Id") . "</td>\n";
                echo "<td>" . nl2br(htmlspecialchars($telegram->OrganizerNotes)) . "</td>\n";
                
                echo "<td>";
                $intrigues = Intrigue::getAllIntriguesForTelegram($telegram->Id, $current_larp->Id);
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
                
                
                echo "<td>" . "<a href='telegram_form.php?operation=update&id=" . $telegram->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>";
                if (empty($intrigues)) echo "<a href='telegram_admin.php?operation=delete&id=" . $telegram->Id . "'><i class='fa-solid fa-trash'></i>";
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