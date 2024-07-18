<?php
 include_once 'header.php';
 
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $subdivision = Subdivision::newFromArray($_POST);
        $subdivision->CampaignId = $current_larp->CampaignId;
        $subdivision->create();
    } elseif ($operation == 'delete') {
        Subdivision::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $subdivision=Subdivision::loadById($_POST['Id']);
        $subdivision->setValuesByArray($_POST);
        $subdivision->update();
    }
    header('Location: subdivision_admin.php');
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    

    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Subdivision::delete($_GET['id']);
        header('Location: subdivision_admin.php');
        exit;
    }
}

include 'navigation.php';
?>

    <div class="content">   
        <h1>Grupperingar</h1>
            <a href="subdivision_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $subdivision_array = Subdivision::allByCampaign($current_larp);
        $resultCheck = count($subdivision_array);
        if ($resultCheck > 0) {
            echo "<table id='subdivision' class='data'>";
            echo "<tr><th>Namn</th><th>Beskrivning</th><th>Antal medlemmar</th><th>Synligt</th><th>Ser medlemmar</th><th></th></tr>\n";
            foreach ($subdivision_array as $subdivision) {
                $memberCount = count($subdivision->getAllMembers());
                echo "<tr>\n";
                echo "<td>".$subdivision->getViewLink()." ".$subdivision->getEditLinkPen()."</td>\n";
                echo "<td>" . $subdivision->Description . "</td>\n";
                echo "<td>" . count($subdivision->getAllMembers()) . "</td>\n";
                echo "<td>" . ja_nej($subdivision->isVisibleToParticipants()) . "</td>\n";
                echo "<td>" . ja_nej($subdivision->canSeeOtherParticipants()) . "</td>\n";
                
                if ($memberCount == 0) {
                    echo "<td>" . "<a href='subdivision_admin.php?operation=delete&id=" . $subdivision->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
                }
                else {
                    echo "<td></td>";
                }
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