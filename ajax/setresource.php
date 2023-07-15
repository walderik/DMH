<?php
include_once 'header.php';

// get the parameters from URL
$resourceId = $_REQUEST["resourceId"];
$titledeedId = $_REQUEST["titledeedId"];
$value = $_REQUEST["value"];
$larpId = $_REQUEST["larpId"];


if (empty($resourceId) || empty($titledeedId)|| empty($value) || empty($larpId)) {
    return;
}
$resource = Resource::loadById($resourceId);
$titledeed = Titledeed::loadById($titledeedId);
$resource_titledeed = Resource_Titledeed::loadByIds($resourceId, $titledeedId);
$larp = LARP::loadById($larpId);

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


