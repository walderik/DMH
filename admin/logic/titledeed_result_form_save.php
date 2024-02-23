<?php
include_once '../header.php';

print_r($_POST);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $titledeed_result = TitledeedResult::newFromArray($_POST);
        $titledeed_result->create();
    } elseif ($operation == 'update') {
        $titledeed_result = TitledeedResult::loadById($_POST['Id']);
        $titledeed_result->setValuesByArray($_POST);
        $titledeed_result->update();
        $titledeed_result->deleteAllUpgradeResults();
    } else {
        header('Location: ../index.php');
        exit;
    }
    
    if (isset($_POST['OrganizerNotes'])) {
        $titledeed = $titledeed_result->getTitledeed();
        $titledeed->OrganizerNotes = $_POST['OrganizerNotes'];
        $titledeed->update();
    }
    if ($titledeed->MoneyForUpgrade > 0) {
        if (isset($_POST['MoneyForUpgradeMet'])) $titledeed_result->createMoneyUpgrade($titledeed->MoneyForUpgrade, true);
        else $titledeed_result->createMoneyUpgrade($titledeed->MoneyForUpgrade, false);
    }
    
    $upgradeDoneResouceIds = array();
    if (isset($_POST['resouceId'])) $upgradeDoneResouceIds = $_POST['resouceId'];
    
    $upgradeRequirements = $titledeed->RequiresForUpgrade();
    foreach ($upgradeRequirements as $upgradeRequirement) {
        
    }
    
    
}

$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../resource_admin.php';
header('Location: ' . $referer);

