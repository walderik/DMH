<?php
 include_once 'header.php';
 
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $operation = $_POST['operation'];
     
    if ($operation == 'insert') {
        $account = Bookkeeping_Account::newFromArray($_POST);
        $account->create();
    } elseif ($operation == 'delete') {
        LARP::delete($_POST['Id']);
    } elseif ($operation == 'update') {
        $account=Bookkeeping_Account::loadById($_POST['Id']);
        $account->setValuesByArray($_POST);
        $account->update();
    }
    header('Location: bookkeeping_account_admin.php');
    exit;
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Bookkeeping_Account::delete($_GET['id']);
        header('Location: bookkeeping_account_admin.php');
        exit;
        
    }
}

include "navigation.php";
?>

    <div class="content">   
        <h1>Konteringskonton</h1>
            <a href="bookkeeping_account_form.php?operation=new"><i class="fa-solid fa-file-circle-plus"></i>Lägg till</a>  
        
        <?php
        
        $account_array = Bookkeeping_Account::getAll($current_larp);
        $resultCheck = count($account_array);
        if ($resultCheck > 0) {
            echo "<table id='accounts' class='data'>";
            echo "<tr><th>Id</td><th>Namn</th><th>Beskrivning</th><th>Valbar</th><th></th><th></th></tr>\n";
            foreach ($account_array as $account) {
                echo "<tr>\n";
                echo "<td>" . $account->Id . "</td>\n";
                echo "<td>" . $account->Name . "</td>\n";
                echo "<td>" . $account->Description . "</td>\n";
                echo "<td>" . ja_nej($account->Active) . "</td>\n";
                
                
                echo "<td>" . "<a href='bookkeeping_account_form.php?operation=update&id=" . $account->Id . "'><i class='fa-solid fa-pen'></i></td>\n";
                echo "<td>";
                if (!$account->inUse()) echo "<a href='bookkeeping_account_admin.php?operation=delete&id=" . $account->Id . "'><i class='fa-solid fa-trash'></i>";
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