<?php
 include_once 'header.php';
 include_once '../includes/selection_data_control.php';
 


 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $operation = $_POST['operation'];



    if ($operation == 'insert') {
        $data = OfficialType::newFromArray($_POST);
        $data->create();
        
        //Lägg till behörigheter på funktionärstypen
        if (isset($_POST['Permission'])) {
            $data->saveAllPermissions($_POST['Permission']);
        }
        
    } elseif ($operation == 'update') {
        $data = OfficialType::loadById($_POST['Id']);
        $data->setValuesByArray($_POST);
        $data->update();
        
        //Uppdatera behörigheter
        $data->deleteAllPermissions();
        if (isset($_POST['Permission'])) {
            $data->saveAllPermissions($_POST['Permission']);
        }
        
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['operation']) && $_GET['operation']=='delete') {
        OfficialType::delete($_GET['id']);
    }
    
}

include 'navigation.php';

?>
    <div class="content">   
        <h1>Typ av funktionärer <a href="forms.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
        <p>Funktionärer kan ha ett antal olika behörigheter som ger dem extra möjligheter i systemet. <br>De behörigheter som finns är:<br>

        <?php 
        
        $permissions = AccessControl::OFFICIAL_ACCESS_TYPES;
        foreach ($permissions as $permission) {
            echo "* $permission<br>";
        }
        
        ?>
        </p>
            <a href="officialtype_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>          
        <?php
        $data_array = OfficialType::allForLarp($current_larp);
        if (count($data_array) > 0) {
            echo "<table class='data'>";
            echo "<tr><th>Namn</th><th>Beskrivning</th><th>Valbar</th><th>Sortering</th><th>Behörigheter</th>";
            echo "<th></th></tr>\n";
            foreach ($data_array as $data) {
                echo "<tr>\n";
                echo "<td><a href='officialtype_form.php?operation=update&id=" . $data->Id . "'>$data->Name</a></td>\n";
                echo "<td>" . nl2br($data->Description) . "</td>\n";
                echo "<td>" . ja_nej($data->Active) . "</td>\n";
                echo "<td>" . $data->SortOrder . "</td>\n";
                echo "<td>";
                //Skriv ut behörigheter
                $permissions = $data->getPermissions();
                $permissionTexts = array();
                foreach ($permissions as $item) $permissionTexts[] = AccessControl::OFFICIAL_ACCESS_TYPES[$item];
                echo implode("<br>", $permissionTexts);
                
                echo "</td>";
                echo "<td>";
                if ($data->mayDelete()) echo "<a href='officialtype_admin.php?type=".$type."&operation=delete&id=" . $data->Id . "'><i class='fa-solid fa-trash'></i>";
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