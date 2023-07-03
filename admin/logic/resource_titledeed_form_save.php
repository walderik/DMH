<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titledeed = Titledeed::loadById($_POST['Id']);
    
    $titledeed->Money = $_POST['Produces_Money'] - $_POST['Requires_Money'];
    $titledeed->update();
    
    $resources = Resource::allNormalByCampaign($current_larp);
    foreach ($resources as $resource) {
        $resource_titledeed = Resource_Titledeed::loadByIds($resource->Id, $titledeed->Id);
        $produces = $_POST['Produces_'.$resource->Id];
        $requires = $_POST['Requires_'.$resource->Id];
        $amount = $produces - $requires;
        $upgrade_amount = $_POST['Upgrade_Required_'.$resource->Id];
        
        if (($amount == 0) && ($upgrade_amount == 0)) {
            if (!empty($resource_titledeed))
                Resource_Titledeed::delete($resource_titledeed->Id);
        } else {
            if (!empty($resource_titledeed)) {
                $resource_titledeed->Quantity = $amount;
                $resource_titledeed->QuantityForUpgrade = $upgrade_amount;
                $resource_titledeed->update();
            }
            else {
                $resource_titledeed = Resource_Titledeed::newWithDefault();
                $resource_titledeed->ResourceId = $resource->Id;
                $resource_titledeed->TitledeedId = $titledeed->Id;
                $resource_titledeed->Quantity = $amount;
                $resource_titledeed->QuantityForUpgrade = $upgrade_amount;
                $resource_titledeed->create();
            }
        }
    }
    
    $referer = (isset($_POST['Referer'])) ? $_POST['Referer'] : '../titledeed_admin.php';
    header('Location: ' . $referer);
}
