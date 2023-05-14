<?php
include_once 'header.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $resource = Resource::newFromArray($_POST);
        $resource->create();
    } elseif ($operation == 'delete') {
        Titledeed::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $resource=Resource::loadById($_POST['Id']);
        $resource->setValuesByArray($_POST);
        $resource->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Resource::delete($_GET['id']);
    }
}

include 'navigation.php';
?>

    <div class="content">
        <h1>Resurser</h1>
            <a href="resource_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
       <?php
    
       $resource_array = Resource::allByCampaign($current_larp);
        if (!empty($resource_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Enhet singular</th><th>Enhet plural</th><th>Pris i Slow River</th><th>Pris i Junk City</th><th>Ovanlig</th><th></th><th></th></tr>\n";
            foreach ($resource_array as $resource) {
                echo "<tr>\n";
                echo "<td>" . $resource->Id . "</td>\n";
                echo "<td>" . $resource->Name . "</td>\n";
                echo "<td>" . $resource->UnitSingular . "</td>\n";
                echo "<td>" . $resource->UnitPlural . "</td>\n";
                echo "<td>" . $resource->PriceSlowRiver . "</td>\n";
                echo "<td>" . $resource->PriceJunkCity . "</td>\n";
                echo "<td>" . ja_nej($resource->IsRare) . "</td>\n";

                
                echo "<td>" . "<a href='resource_form.php?operation=update&id=" . $resource->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='resource_admin.php?operation=delete&id=" . $resource->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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