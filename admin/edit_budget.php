<?php

include_once 'header.php';



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
		<h1>Budget för <?php echo $current_larp->Name?></h1>
		<p>Skriv "-" om det är utgifter och "+" om det är inkomster.</p>
		<form action="logic/edit_budget_save.php" method="post">
			<table>
			<tr><td>Konto</td><td>Fast summa</td><td>Summa per person<br>Både deltagare och funktionärer</td></tr>
    		<?php 
    		$budgets = Budget::getAll($current_larp);
    		$accounts = Bookkeeping_Account::allActiveIncludeCommon($current_larp);

    		
    		foreach ($accounts as $account) {
    		    echo "<tr>";
    		    echo "<td>$account->Name</td>";
    		    

    		    $budgetItem = getBudget($budgets, $account);
    		    
    		    echo "<td><input type='number' id='budget[$account->Id][0]' name='budget[$account->Id][0]' ";
                echo "value='$budgetItem->FixedAmount' maxlength='10' required></td>";
    		    echo "<td><input type='number' id='budget[$account->Id][1]' name='budget[$account->Id][1]' value='$budgetItem->AmountPerPerson' maxlength='10' required></td>";
    		    
     		    
    		    echo "</tr>";
    		}
    		
    		
    		
    		?>


			</table>
        	<button type="submit">Spara</button>
        	</form>
		</div>
		


</body>
</html>
