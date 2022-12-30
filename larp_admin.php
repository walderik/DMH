<?php
include_once 'includes/db.inc.php';
require 'models/LARP.php';

?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Administration av lajv</title>
<link rel="stylesheet" href="includes/admin_system.css">

</head>
<body>
    
   
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
//     echo $operation;
    if ($operation == 'insert') {
        $larp = LARP::newFromArray($_POST);
        $larp->create();
    } elseif ($operation == 'delete') {
        LARP::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $larp = LARP::newFromArray($_POST);
        $larp->update();
    } else {
        echo $operation;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
//     $operation = $_GET['operation'];
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        LARP::delete($_GET['id']);
    }
}

?>
    
    <h1>Lajv</h1>
        <a href="larp_form.php?operation=new"><img src='images/icons8-add-new-50.png' alt='Lägg till'/></a>  
    
    <?php
    
    $larp_array = LARP::all();
    $resultCheck = count($larp_array);
    if ($resultCheck > 0) {
        echo "<table id='larps' class='data'>";
        echo "<tr><th>Id</td><th>Namn</th><th>Förkortning</th><th>Tag line</th><th>Startdatum</th><th>Slutdatum stad</th><th>Max deltagare</th><th>Sista anmälningsdag</th><th>Start lajvtid</th><th>Slut lajvtid</th><th></th><th></th></tr>\n";
        foreach ($larp_array as $larp) {
            echo "<tr>\n";
            echo "<td>" . $larp->Id . "</td>\n";
            echo "<td>" . $larp->Name . "</td>\n";
            echo "<td>" . $larp->Abbreviation . "</td>\n";
            echo "<td>" . $larp->TagLine . "</td>\n";
            echo "<td>" . $larp->Startdate . "</td>\n";
            echo "<td>" . $larp->EndDate . "</td>\n";
            echo "<td>" . $larp->MaxParticipants . "</td>\n";
            echo "<td>" . $larp->LatestRegistrationDate . "</td>\n";
            echo "<td>" . $larp->StartTimeLARPTime . "</td>\n";
            echo "<td>" . $larp->EndTimeLARPTime . "</td>\n";
            
            echo "<td>" . "<a href='larp_form.php?operation=update&id=" . $larp->Id . "'><img src='images/icons8-pencil-20.png' width='20' alt='Redigera' /></a></td>\n";
            echo "<td>" . "<a href='larp_admin.php?operation=delete&id=" . $larp->Id . "'><img src='images/icons8-trash-20.png' width='20' alt='Radera' /></a></td>\n";
            echo "</tr>\n";
        }
        echo "</table>";
    }
    else {
        echo "<p>Inga registrarade ännu</p>";
    }
    ?>
    
	
<p>
Icons by <a href="https://icons8.com">Icons8</a>
</p>
</body>

</html>