<?php

include_once 'header.php';

$comparison = 0;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['comparison'])) $comparison = $_POST['comparison'];
}


function getBudget($budgets, $account) {
    foreach ($budgets as $budget) {
        if ($budget->AccountId == $account->Id) {
            return $budget;
        }
    }
    return Budget::newWithDefault();
}

include 'navigation.php';
?>

	<div class="content">
		<h1>Budget för <?php echo $current_larp->Name?>
		<a href="edit_budget.php"><i class='fa-solid fa-pen'></i></a>
		</h1>
		
		<div>
			<table  class='data'>
			<tr><td rowspan='2'></td><td rowspan='2'>Budget för det här lajvet</td>
			<td colspan='2'>
    			<form method="POST">
        			<select name='comparison' id='comparison'>
        
        				<?php 
        				echo "<option value='0'";
        				if ($comparison == 0) echo " selected ";
        				echo ">Utfall för det här lajvet</option>\n";
        				
        				$larps = LARP::allByCampaign($current_larp->CampaignId);
        				foreach ($larps as $larp) {
        				    if ($larp->Id != $current_larp->Id) {
        				        echo "<option value='$larp->Id'";
        				        if ($comparison == $larp->Id) echo " selected ";
        				        echo ">Budget och utfall $larp->Name</option>\n";
        				    }
        				}
        				?>
        			</select>
        			<input type='submit' value='Visa'>
    
    			</form>
			
			</td>
			</tr>

    			<tr>
			<?php if ($comparison != 0) { ?>
			    			<td style='font-weight: normal; background-color: #ffffff;'>Budget</td><td style='font-weight: normal; background-color: #ffffff;'>Utfall</td>
			<?php }?>
    			</tr>
    		<?php 
    		$budgets = Budget::getAll($current_larp);
    		$accounts = Bookkeeping_Account::allActiveIncludeCommon($current_larp);
    		$numberOfPersons = count(Registration::allBySelectedLARP($current_larp));
    		if ($comparison != 0) {
    		    $comparisonLarp = LARP::loadById($comparison);
    		    $comparisonLarpBudgets = Budget::getAll($comparisonLarp);
    		    $comparisonNumberOfPersons = count(Registration::allBySelectedLARP($comparisonLarp));
    		}
    		$sum = 0;
    		$comparisonSum = 0;
    		$comparisonSum2 = 0;
    		
    		foreach ($accounts as $account) {
    		    echo "<tr>";
    		    echo "<td>$account->Name</td>";
    		    

    		    $budgetItem = getBudget($budgets, $account);
    		    if (null !== $budgetItem->AmountPerPerson) $amount = $budgetItem->FixedAmount + $budgetItem->AmountPerPerson * $numberOfPersons;
    		    else $amount = $budgetItem->FixedAmount;
    		    
    		    echo "<td style='text-align: right;";
    		    if ($amount < 0) echo "color:red;";
    		    echo "'>". number_format((float)$amount, 2, ',', '')."</td>";
    		    $sum += $amount;
    		    
    		    

    		    if ($comparison == 0) {
    		        if (in_array($account->Id, Bookkeeping_Account::COMMON_ACCOUNTS)) {
    		            if ($account->Id == Bookkeeping_Account::FEES_ACCOUNT) $comparisonAmount = Registration::totalFeesPayed($current_larp);
    		            elseif ($account->Id == Bookkeeping_Account::RETURNED_FEES_ACCOUNT) $comparisonAmount = 0-Registration::totalFeesReturned($current_larp);
    		            elseif ($account->Id == Bookkeeping_Account::INVOICE_ACCOUNT) $comparisonAmount = Invoice::getInvoiceSum($current_larp);
    		            else $comparisonAmount = 0; //Borde aldrig komma hit.
    		        }
    		        else $comparisonAmount = Bookkeeping::amountOnAccount($current_larp, $account);
    		    }
    		    else {
    		        $comparisonBudgetItem = getBudget($comparisonLarpBudgets, $account);
    		        if (null !== $comparisonBudgetItem->AmountPerPerson) {
    		            $comparisonAmount = $comparisonBudgetItem->FixedAmount + $comparisonBudgetItem->AmountPerPerson * $comparisonNumberOfPersons;
    		        }
    		        else $comparisonAmount = $comparisonBudgetItem->FixedAmount;
    		        
    		        
    		    }
    		    echo "<td style='text-align: right;";
    		    if ($comparisonAmount < 0) echo "color:red;";
    		    echo "'>". number_format((float)$comparisonAmount, 2, ',', '')."</td>";
    		    $comparisonSum += $comparisonAmount;
    		    
    		    
    		    if ($comparison != 0) {
    		        $comparisonAmount = Bookkeeping::amountOnAccount($comparisonLarp, $account);
    		        echo "<td style='text-align: right;";
    		        if ($comparisonAmount < 0) echo "color:red;";
    		        echo "'>". number_format((float)$comparisonAmount, 2, ',', '')."</td>";
    		        $comparisonSum2 += $comparisonAmount;
    		    }
    		    
    		    echo "</tr>";
    		}
    		
    		echo "<tr><th>Summa</th><th style='text-align: right;";
    		if ($sum < 0) echo "color:red;";
    		echo "'>". number_format((float)$sum, 2, ',', '')."</th>";
    		echo "<th style='text-align: right;";
    		if ($comparisonSum < 0) echo "color:red;";
    		echo "'>". number_format((float)$comparisonSum, 2, ',', '')."</th>";

    		if ($comparison != 0) {
    		    echo "<th style='text-align: right;";
        		if ($comparisonSum2 < 0) echo "color:red;";
        		echo "'>". number_format((float)$comparisonSum2, 2, ',', '')."</th>";
    		}
    		
    		echo "</tr>";
    		
    		?>


			</table>

		</div>
		


</body>
</html>
