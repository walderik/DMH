<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $budgetMatrix = $_POST['budget'];
    
    foreach ($budgetMatrix as $key => $budgetArray) {
        $account = Bookkeeping_Account::loadById($key);
        $budgetItem = Budget::getByAccount($current_larp, $account);
        if (!isset($budgetItem)) {
            $budgetItem = Budget::newWithDefault();
            $budgetItem->AccountId = $account->Id;
            $budgetItem->create();
        }
        $budgetItem->FixedAmount = $budgetArray[0];
        $budgetItem->AmountPerPerson = $budgetArray[1];
        $budgetItem->update();
    }

}
header('Location: ../view_budget.php');