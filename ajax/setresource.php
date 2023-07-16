<?php
include_once 'header.php';

// get the parameters from URL
if (isset($_REQUEST["resourceId"])) $resourceId = $_REQUEST["resourceId"];
$titledeedId = $_REQUEST["titledeedId"];
$value = $_REQUEST["value"];
$larpId = $_REQUEST["larpId"];


if (empty($titledeedId)|| empty($value) || empty($larpId)) {
    return;
}

$titledeed = Titledeed::loadById($titledeedId);
$larp = LARP::loadById($larpId);

if (empty($resourceId)) {
    //Money
    $titledeed->Money = $value;
    $titledeed->update();
    echo $titledeed->calculateResult()." ".$titledeed->moneySum($larp);
    
} else {
   //Resource 

    $resource = Resource::loadById($resourceId);
    $resource_titledeed = Resource_Titledeed::loadByIds($resourceId, $titledeedId);
    
    if (isset($resource_titledeed)) {
        if (($value == 0) && ($resource_titledeed->QuantityForUpgrade == 0)) {
            Resource_Titledeed::delete($resource_titledeed->Id);
        }
        else {
            $resource_titledeed->Quantity = $value;
            $resource_titledeed->update();
        }
    } else {
        $resource_titledeed = Resource_Titledeed::newWithDefault();
        $resource_titledeed->ResourceId = $resourceId;
        $resource_titledeed->TitledeedId = $titledeedId;
        $resource_titledeed->Quantity = $value;
        $resource_titledeed->create();
    }
    
    echo $titledeed->calculateResult()." ".$resource->countBalance($larp)." ".$resource->countNumberOfCards($larp);

}
