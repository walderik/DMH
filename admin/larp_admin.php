 <?php
 include_once 'header.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     //echo $operation;
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

        <nav id="navigation">
          <a href="#" class="logo"><?php echo $current_larp->Name;?></a>
          <ul class="links">
            <li><a href="index.php"><i class="fa-solid fa-house"></i></i>Hem</a></li>
        	<li><a href="../includes/logout.php"><i class="fa-solid fa-right-from-bracket"></i>Logga ut</a></li>
          </ul>
        </nav>

    <div class="content">   
        <h1>Lajv</h1>
            <a href="larp_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $larp_array = LARP::all();
        $resultCheck = count($larp_array);
        if ($resultCheck > 0) {
            echo "<table id='larp' class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Förkortning</th><th>Tag line</th><th>Startdatum</th><th>Slutdatum stad</th><th>Max deltagare</th><th>Sista anmälningsdag</th><th>Start lajvtid</th><th>Slut lajvtid</th><th></th><th></th></tr>\n";
            foreach ($larp_array as $larp) {
                echo "<tr>\n";
                echo "<td>" . $larp->Id . "</td>\n";
                echo "<td>" . $larp->Name . "</td>\n";
                echo "<td>" . $larp->Abbreviation . "</td>\n";
                echo "<td>" . $larp->TagLine . "</td>\n";
                echo "<td>" . $larp->StartDate . "</td>\n";
                echo "<td>" . $larp->EndDate . "</td>\n";
                echo "<td>" . $larp->MaxParticipants . "</td>\n";
                echo "<td>" . $larp->LatestRegistrationDate . "</td>\n";
                echo "<td>" . $larp->StartTimeLARPTime . "</td>\n";
                echo "<td>" . $larp->EndTimeLARPTime . "</td>\n";
                
                echo "<td>" . "<a href='larp_form.php?operation=update&id=" . $larp->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>" . "<a href='larp_admin.php?operation=delete&id=" . $larp->Id . "'><i class='fa-solid fa-trash'></i></td>\n";
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