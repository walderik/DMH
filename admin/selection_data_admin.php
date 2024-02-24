<?php
 include_once 'header.php';
 include_once '../includes/selection_data_control.php';
 
 if (!AccessControl::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)  || isset($_SESSION['admin'])) {
     exit;
 }
 
 $type;
 $objectType;
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['type'])) {
        $type=$_POST['type'];
    }
    else {
        header('Location: index.php');
        exit;
        
    }
    $objectType = getObjectType($type);

    $operation = $_POST['operation'];



    if ($operation == 'insert') {
        $user_func = $objectType . '::newFromArray';
        $data = call_user_func($user_func, $_POST);
        $data->create();
    } elseif ($operation == 'update') {
        $user_func = $objectType . '::loadById';
        $data = call_user_func($user_func,$_POST['Id']);
        $data->setValuesByArray($_POST);
        $data->update();
    } 
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (isset($_GET['type'])) {
        $type=$_GET['type'];
    }
    else {
        header('Location: index.php');
        exit;
        
    }
    $objectType = getObjectType($type);
    if (isset($_GET['operation']) && $_GET['operation']=='delete') {
        call_user_func($objectType . '::delete', $_GET['id']);
    }
    
}

include 'navigation.php';

?>
    <div class="content">   
        <h1><?php echo getObjectName($type);?> <a href="forms.php"><i class="fa-solid fa-arrow-left" title="Tillbaka"></i></a></h1>
            <a href="selection_data_form.php?type=<?php echo $type;?>&operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>          
        <?php
        $data_array = call_user_func($objectType . '::allForLarp', $current_larp);
        if (count($data_array) > 0) {
            echo "<table class='data'>";
            echo "<tr><th>Namn</th><th>Beskrivning</th><th>Valbar</th><th>Sortering</th>";
            if ($objectType == 'IntrigueType') {
                echo "<th>För vilka<br>finns typen</th>";
            }
            echo "<th></th></tr>\n";
            foreach ($data_array as $data) {
                echo "<tr>\n";
                echo "<td><a href='selection_data_form.php?type=".$type."&operation=update&id=" . $data->Id . "'>$data->Name</a></td>\n";
                echo "<td>" . nl2br($data->Description) . "</td>\n";
                echo "<td>" . $data->Active . "</td>\n";
                echo "<td>" . $data->SortOrder . "</td>\n";
                if ($objectType == 'IntrigueType') {
                    echo "<td>".$data->getForString()."</td>";
                }
                
                echo "<td>";
                if ($data->mayDelete()) echo "<a href='selection_data_admin.php?type=".$type."&operation=delete&id=" . $data->Id . "'><i class='fa-solid fa-trash'></i>";
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