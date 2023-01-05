 <?php
 include_once 'header.php';
 include_once '../includes/selection_data_control.php';
 
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
//     echo $type."<br />";
//     echo $objectType."<br />";
    $user_func = $objectType . '::newFromArray';
//     echo $user_func."<br />";
    if ($operation == 'insert') {
        $data = call_user_func($user_func, $_POST);
//         print_r($data)."<br />";
        $data->create();
    } elseif ($operation == 'update') {
        $data = call_user_func($user_func, $_POST);
        $data->update();
    } else {
        echo $operation;
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
}



?>
	<nav class="navtop">
		<div>
			<h1><?php echo $current_larp->Name;?></h1>
			<a href="index.php"><i class="fa-solid fa-house"></i>Hem</a>
			<a href="/includes/logout.php"><i class="fas fa-sign-out-alt"></i>Logout</a>
		</div>
	</nav>
    <div class="content">   
        <h1><?php echo getObjectName($type);?></h1>
            <a href="selection_data_form.php?type=<?php echo $type;?>&operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>          
        <?php
        $data_array = call_user_func($objectType . '::all');
        if (count($data_array) > 0) {
            echo "<table class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Beskrivning</th><th>Aktiv</th><th>Sortering</th><th></th></tr>\n";
            foreach ($data_array as $data) {
                echo "<tr>\n";
                echo "<td>" . $data->Id . "</td>\n";
                echo "<td>" . $data->Name . "</td>\n";
                echo "<td>" . $data->Description . "</td>\n";
                echo "<td>" . $data->Active . "</td>\n";
                echo "<td>" . $data->SortOrder . "</td>\n";
              
                echo "<td>" . "<a href='selection_data_form.php?type=".$type."&operation=update&id=" . $data->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
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