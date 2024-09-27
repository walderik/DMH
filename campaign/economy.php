<?php
include_once 'header.php';
// include_once '../includes/error_handling.php';

$years = array_reverse(LARP::getAllYears());
$choosen_year = date("Y");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['year'])) $choosen_year = $_POST['year'];
    if (isset($_POST['operation'])) {
        $operation = $_POST['operation'];
        
        if ($operation == 'add_income') {
            $bookkeeping = Bookkeeping::newFromArray($_POST);
            $bookkeeping->CampaignId = $current_larp->CampaignId;
            $bookkeeping->LarpId = NULL;
            $bookkeeping->create();
        } elseif ($operation == 'add_expense') {
            $bookkeeping = Bookkeeping::newFromArray($_POST);
            $bookkeeping->Amount = 0 - $_POST['Amount'];
            $bookkeeping->create();
            saveReceipt($bookkeeping);
        } elseif ($operation == 'update_income') {
            $bookkeeping = Bookkeeping::loadById($_POST['id']);
            $bookkeeping->setValuesByArray($_POST);
            $bookkeeping->update();
        } elseif ($operation == 'update_expense') {
            $bookkeeping = Bookkeeping::loadById($_POST['id']);
            $bookkeeping->setValuesByArray($_POST);
            $bookkeeping->Amount = 0 - $_POST['Amount'];
            
            $bookkeeping->update();
            saveReceipt($bookkeeping);
        }
        header("Location: economy.php");
        exit;
    }
    //if (isset($error)) header("Location: economy.php?error=$error");
    //else header('Location: ' . 'economy.php');
    //exit;
    
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['operation']) && $_GET['operation'] == 'delete') {
        Bookkeeping::delete($_GET['id']);
        header("Location: economy.php");
        exit;
    }
}


function saveReceipt(Bookkeeping $bookkeeping) {
    global $error_code, $error_message;
    if (!empty($_FILES["upload"]["name"])) {
        $error = Image::maySave(true);
        if (!isset($error)) {
            $id = Image::saveImage("Verifikation $bookkeeping->Number", true);
            $bookkeeping->ImageId = $id;
            $bookkeeping->update();
        } else {
            $error_code = $error;
            $error_message = getErrorText($error_code);
        }
    }
    
}

$campaign = $current_larp->getCampaign();


// include_once '../includes/error_handling.php';


include 'navigation.php';
?>

<style>
.amount {
  text-align: right;
}

</style>

    <div class="content">
        <h1>Kassabok</h1>
        	  <?php if (isset($error_message) && strlen($error_message)>0) {
	      echo '<div class="error">'.$error_message.'</div>';
	  }?>
	  <?php if (isset($message_message) && strlen($message_message)>0) {
	      echo '<div class="message">'.$message_message.'</div>';
	  }?>
        <p>Lägg in alla inkomster och utgifter som kampanjen har som inte är direkt kopplade till ett lajv.  
        Vid årets slut är det bara att plocka ut de två filerna som ska till kassören och maila in så följer allt om kampanjens lajv med.<br><br>
        En varning betyder att det saknas ett kvitto på en utgift.<br><br>
        Konton läggs upp under <a href="settings.php">inställningar</a></p>
        <p>
        <a href="economy_form.php?operation=add_income"><i class="fa-solid fa-file-circle-plus"></i> Lägg till inkomst</a> &nbsp; 
        <a href="economy_form.php?operation=add_expense"><i class="fa-solid fa-file-circle-plus"></i> Lägg till utgift</a><br>
        
        
 
        <a href="reports/economy.php?year=<?php echo $choosen_year?>" target="_blank"><i class="fa-solid fa-file-pdf"></i> Generera rapport till kassör</a> &nbsp; 
        <a href="logic/all_bookkeeping_zip.php?year=<?php echo $choosen_year?>" target="_blank"><i class="fa-solid fa-file-zipper"></i> Alla verifikationer till kassör</a><br>

        </p>
        <form method="POST">
        
        
        <select name="year" id="year">
        <?php 
        foreach ($years as $year) {
          echo "<option value='$year'";
          if (isset($choosen_year) && $year == $choosen_year) echo " selected ";
          echo ">$year</option>";   
        }
        ?>
        </select>
        
        <input type='submit' value='Visa'>
        
        </form>
                
        
		<?php 
		$bookkeepings = Bookkeeping::allUnFinishedCampaign($campaign,$choosen_year);
	   //if (!empty($bookkeepings)) {
           echo "<h2>Påbörjade</h2>"; 
           echo "Alla som saknar bokföringsdatum hamnar här.";
           echo "<table id='bookkeeping' class='data'>";
           echo "<tr><th>Upplagd datum</th><th>Rubrik</th><th>Ansvarig</th><th>Summa</th><th></th></tr>\n";
           foreach ($bookkeepings as $bookkeeping) {
               echo "<tr>\n";

               echo "<td>" . $bookkeeping->CreationDate . "</td>\n";
               echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
               if ($bookkeeping->Amount < 0 && !$bookkeeping->hasImage()) {
                   echo " " . showStatusIcon(false);
               }
               
               if ($bookkeeping->Amount > 0) {
                   echo " <a href='economy_form.php?operation=update_income&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra inkomst'></i></a>";
                   echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
               } else {
                   echo " <a href='economy_form.php?operation=update_expense&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra utgift'></i></a>";
                   
               }
               echo "</td>\n";
               
               echo "<td>".$bookkeeping->getUser()->Name."</td>";
               echo "<td class='amount'>" .number_format((float)$bookkeeping->Amount, 2, ',', '')."</td>\n";
               echo "<td>";
               echo "<a href='economy.php?operation=delete&id=$bookkeeping->Id'><i class='fa-solid fa-trash' title='Radera'></i></a>";
               echo "</td>";
               echo "</tr>\n";
           }
           echo "</table>";
	   //}
	
	
	   
           $bookkeepings = Bookkeeping::allFinishedCampaign($campaign, $choosen_year);
	   //if (!empty($bookkeepings)) {
	   echo "<h2>Klara</h2>";
       $sum = 0;
       echo "<table id='bookkeeping' class='data'>";
       echo "<tr><th>Verifikation<br>nummer</th><th>Bokföringsdatum</th><th>Rubrik</th><th>Konto</th><th>Ansvarig</th><th>Summa</th></tr>\n";
       echo "<tr><td></td><td></td><td>Buffert</td><td></td><td></td><td class='amount' style='text-align: right;'>" .number_format((float)10000, 2, ',', '')."</td></tr>";
       foreach ($bookkeepings as $bookkeeping) {
           echo "<tr>\n";
           echo "<td>" . $bookkeeping->Number . "</td>\n";
           echo "<td>" . $bookkeeping->AccountingDate . "</td>\n";
           echo "<td><a href='economy_view_bookkeeping.php?id=$bookkeeping->Id'>" . $bookkeeping->Headline."</a>";
           if ($bookkeeping->Amount < 0 && !$bookkeeping->hasImage()) {
               echo " " . showStatusIcon(false);
               echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
               
           }
           if ($bookkeeping->Amount > 0) {
               echo " <a href='economy_form.php?operation=update_income&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra inkomst'></i></a>";
               echo " <a href='economy_receipt_pdf.php?bookkeepingId=$bookkeeping->Id' target='_blank'><i class='fa-solid fa-file-pdf' title='Visa kvitto'></i></a>";
           } else {
               echo " <a href='economy_form.php?operation=update_expense&id=$bookkeeping->Id'><i class='fa-solid fa-pen' title='Ändra utgift'></i></a>";
               
           }
           echo "</td>\n";
           echo "<td>" . $bookkeeping->getBookkeepingAccount()->Name . "</td>"; 
           echo "<td>".$bookkeeping->getPerson()->Name."</td>";
           
           echo "<td class='amount'>" .number_format((float)$bookkeeping->Amount, 2, ',', '')."</td>\n";
           $sum += $bookkeeping->Amount;
           echo "</tr>\n";
       }


       $larps = LARP::getAllForYear($campaign->Id, $choosen_year);
       if (!empty($larps)) {
           
           foreach ($larps as $larp) {
               
               $income = Registration::totalIncomeToday($larp) + Bookkeeping::sumRegisteredIncomes($larp);
               $refund = 0 - Registration::totalFeesReturned($larp);
               $expense = Bookkeeping::sumRegisteredExpenses($larp);
               $larp_sum = $income + $refund + $expense;
               
               $sum += $larp_sum;
               
               echo "<tr><td></td><td></td><td>$larp->Name</td><td></td><td></td><td class='amount'>".number_format((float)$larp_sum, 2, ',', '')."</td></tr>";
           }

       }

       
       echo "<tr></tr>";
       echo "<tr><th colspan='5'>Balans</th><th class='amount' style='text-align: right;'>".number_format((float)$sum, 2, ',', '')."</th></tr>";
       echo "<tr><th colspan='5'>På kontot</th><th class='amount' style='text-align: right;'>".number_format((float)$sum+10000, 2, ',', '')."</th></tr>";
       echo "</table>";
	   //}
       ?>
       
       
	


</body>
</html>