<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $operation = $_POST['operation'];
    
    if ($operation == 'insert') {
        $titledeed_result = TitledeedResult::newFromArray($_POST);
        $titledeed_result->create();
        $titledeed = $titledeed_result->getTitledeed();
        
        $upgradeDoneResouceIds = array();
        if (isset($_POST['resouceId'])) $upgradeDoneResouceIds = $_POST['resouceId'];
        
        $upgradeRequirements = $titledeed->RequiresForUpgrade();
        foreach ($upgradeRequirements as $upgradeRequirement) {
            $upgrade = Titledeedresult_Upgrade::newWithDefault();
            $upgrade->TitledeedResultId = $titledeed_result->Id;
            $upgrade->ResourceId = $upgradeRequirement->ResourceId;
            $upgrade->QuantityForUpgrade = $upgradeRequirement->QuantityForUpgrade;
            if (in_array($upgradeRequirement->ResourceId, $upgradeDoneResouceIds)) $upgrade->NeedsMet = 1;
            $upgrade->NeedsMet = 0;
            $upgrade->create();
        }
        if ($titledeed->MoneyForUpgrade > 0) {
            $upgrade = Titledeedresult_Upgrade::newWithDefault();
            $upgrade->TitledeedResultId = $titledeed_result->Id;
            $upgrade->ResourceId = null;
            $upgrade->QuantityForUpgrade = $titledeed->MoneyForUpgrade;
            if (isset($_POST['MoneyForUpgradeMet'])) $upgrade->NeedsMet = 1;
            else $upgrade->NeedsMet = 0;
            $upgrade->create();
        }
        
        
        
    } elseif ($operation == 'update') {
        $titledeed_result = TitledeedResult::loadById($_POST['Id']);
        $titledeed_result->setValuesByArray($_POST);
        $titledeed_result->update();
        
        $upgradeDoneResouceIds = array();
        if (isset($_POST['resouceId'])) $upgradeDoneResouceIds = $_POST['resouceId'];
        
        $upgrade_results = Titledeedresult_Upgrade::getAllUpgradeResults($titledeed_result);
        foreach ($upgrade_results as $upgrade_result) {

            if (isset($upgrade_result->ResourceId)) {
                $upgrade_result->NeedsMet = in_array($upgrade_result->ResourceId, $upgradeDoneResouceIds);
                $upgrade_result->update();
            } else {
                $upgrade_result->NeedsMet = isset($_POST['MoneyForUpgradeMet']);
                $upgrade_result->update();
            }
            
        }
        
        
    } else {
        header('Location: ../index.php');
        exit;
    }
    
    if (isset($_POST['OrganizerNotes'])) {
        $titledeed = $titledeed_result->getTitledeed();
        $titledeed->OrganizerNotes = $_POST['OrganizerNotes'];
        $titledeed->update();
    }
    

    
    
    
}

$referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../resource_admin.php';
header('Location: ' . $referer);

