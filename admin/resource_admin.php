<?php
include_once 'header.php';



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Resource::delete($_GET['Id']);
    }
}

include 'navigation.php';
?>

    <div class="content">
        <h1>Resurser <a href="commerce.php"><i class="fa-solid fa-arrow-left" title="Tillbaka till handel"></i></a></h1>
            <a href="resource_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
            <p>Resurser går bara att radera om de inte används av någon verksamhet. 
            Det inkluderar normalt producerar / behöver och vad det producerar / behöver / behöver för uppgradering, samt inmatade resultat på specifika lajv.</p>
        
       <?php
    
       $resource_array = Resource::allByCampaign($current_larp);
        if (!empty($resource_array)) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Enhet singular</th><th>Enhet plural</th><th>Antal per vagn</th><th>Pris</th><th>Ovanlig</th><th>Bild</th><th></th><th></th></tr>\n";
            foreach ($resource_array as $resource) {
                echo "<tr>\n";
                echo "<td>" . $resource->Id . "</td>\n";
                echo "<td>" . $resource->Name . "</td>\n";
                echo "<td>" . $resource->UnitSingular . "</td>\n";
                echo "<td>" . $resource->UnitPlural . "</td>\n";
                echo "<td>" . $resource->AmountPerWagon . "</td>\n";
                echo "<td>" . $resource->Price . "</td>\n";
                echo "<td>" . ja_nej($resource->IsRare) . "</td>\n";

                if ($resource->hasImage()) {
                    echo "<td><img width='30' src='../includes/display_image.php?id=$resource->ImageId'/>\n";
                    echo " <a href='logic/delete_image.php?id=$resource->Id&type=resource'><i class='fa-solid fa-trash' title='Ta bort bild'></i></a></td>\n";
                }
                else {
                    echo "<td><a href='upload_image.php?id=$resource->Id&type=resource'><i class='fa-solid fa-image-portrait' title='Ladda upp bild'></i></a></td>\n";
                }
                
                echo "<td>" . "<a href='resource_form.php?operation=update&Id=" . $resource->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>";
                if ($resource->mayDelete()) echo "<a href='resource_admin.php?operation=delete&Id=" . $resource->Id . "'><i class='fa-solid fa-trash'></i>";
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