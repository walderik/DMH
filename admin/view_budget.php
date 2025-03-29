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
			<table>
			<tr><td></td><td>Budget</td>
			<td>
    			<form method="POST">
        			<select name='comparison' id='comparison'>
        
        				<?php 
        				echo "<option value='0'";
        				if ($comparison == 0) echo " selected ";
        				echo ">Utfall $current_larp->Name</option>\n";
        				
        				$larps = LARP::allByCampaign($current_larp->CampaignId);
        				foreach ($larps as $larp) {
        				    if ($larp->Id != $current_larp->Id) {
        				        echo "<option value='$larp->Id'";
        				        if ($comparison == $larp->Id) echo " selected ";
        				        echo ">Budget $larp->Name</option>\n";
        				    }
        				}
        				?>
        			</select>
        			<input type='submit' value='Visa'>
    
    			</form>
			
			</td>
			</tr>
    		<?php 
    		$budgets = Budget::getAll($current_larp);
    		$accounts = Bookkeeping_Account::allActive($current_larp);
    		$numberOfPersons = count(Registration::allBySelectedLARP($current_larp));
    		if ($comparison != 0) {
    		    $comparisonLarp = LARP::loadById($comparison);
    		    $comparisonLarpBudgets = Budget::getAll($comparisonLarp);
    		    $comparisonNumberOfPersons = count(Registration::allBySelectedLARP($comparisonLarp));
    		}
    		
    		foreach ($accounts as $account) {
    		    echo "<tr>";
    		    echo "<td>$account->Name</td>";
    		    

    		    $budgetItem = getBudget($budgets, $account);
    		    if (null !== $budgetItem->AmountPerPerson) $sum = $budgetItem->FixedAmount + $budgetItem->AmountPerPerson * $numberOfPersons;
    		    else $sum = $budgetItem->FixedAmount;
    		    
    		    echo "<td align='right'>$sum</td>";
    		    
    		    
    		    
    		    echo "<td align='right' id=account_$account->Id>";
    		    if ($comparison == 0) echo number_format((float)Bookkeeping::amountOnAccount($current_larp, $account), 2, ',', '')."</td>";
    		    else {
    		        $comparisonBudgetItem = getBudget($comparisonLarpBudgets, $account);
    		        if (null !== $comparisonBudgetItem->AmountPerPerson) {
    		            $comparisonSum = $comparisonBudgetItem->FixedAmount + $comparisonBudgetItem->AmountPerPerson * $comparisonNumberOfPersons;
    		        }
    		        else $comparisonSum = $comparisonBudgetItem->FixedAmount;
    		        
    		        echo "<td align='right'>$comparisonSum</td>";
    		        
    		    }
    		    
    		    echo "</tr>";
    		}
    		
    		
    		
    		?>


			</table>

		</div>
		


</body>
</html>
