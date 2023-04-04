<?php
include_once 'header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $titledeed = Titledeed::newFromArray($_POST);
        $titledeed->create();
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $titledeed = Titledeed::newFromArray($_POST);
        $titledeed->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Titledeed::delete($_GET['id']);
    }
}

include 'navigation_subpage.php';
?>

    <div class="content">
        <h1>Lagfarter</h1>
            <a href="titledeed_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
       <?php
    
       $titledeed_array = Titledeed::allByCampaign($current_larp);
        if (!empty($titledeed_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Plats</th><th>Kan säljas</th><th>Handelsstation</th><th></th><th></th></tr>\n";
            foreach ($titledeed_array as $titledeed) {
                echo "<tr>\n";
                echo "<td>" . $titledeed->Id . "</td>\n";
                echo "<td>" . $titledeed->Name . "</td>\n";
                echo "<td>" . $titledeed->Location . "</td>\n";
                echo "<td>" . ja_nej($titledeed->Tradeable) . "</td>\n";
                echo "<td>" . ja_nej($titledeed->IsTradingPost) . "</td>\n";
                
                echo "<td>" . "<a href='titledeed_form.php?operation=update&id=" . $titledeed->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='titledeed_admin.php?operation=delete&id=" . $titledeed->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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