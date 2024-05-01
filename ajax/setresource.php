<?php
include_once 'header.php';

// get the parameters from URL
if (isset($_REQUEST["resourceId"])) $resourceId = $_REQUEST["resourceId"];
$titledeedId = $_REQUEST["titledeedId"];
$value = $_REQUEST["value"];
$larpId = $_REQUEST["larpId"];


if (empty($titledeedId)|| !isset($value) || empty($larpId)) {
    return;
}

$titledeed = Titledeed::loadById($titledeedId);
$larp = LARP::loadById($larpId);

if (empty($resourceId)) {
    //Money
    $titledeed->Money = $value;
    $titledeed->update();
    $money_upgrade = $titledeed->moneySum($larp)-$titledeed->moneySumUpgrade($larp);
    echo $titledeed->calculateResult()." ".$titledeed->moneySum($larp)." ".$money_upgrade;
    
} else {
   //Resource 

    $resource = Resource::loadById($resourceId);
    $resource_titledeed = Resource_Titledeed::loadByIds($resourceId, $titledeedId);
    if ($resource->isRare()) {
        if (isset($resource_titledeed)) {
            if (($value == 0)) {
                Resource_Titledeed::delete($resource_titledeed->Id);
            }
            elseif ($value > 0) {
                $resource_titledeed->Quantity = $value;
                $resource_titledeed->update();
            } else {
                $resource_titledeed->QuantityForUpgrade = abs($value);
                $resource_titledeed->update();
            }
        } else {
            $resource_titledeed = Resource_Titledeed::newWithDefault();
            $resource_titledeed->ResourceId = $resourceId;
            $resource_titledeed->TitledeedId = $titledeedId;
            if ($value > 0) {
                $resource_titledeed->Quantity = $value;
            } else {
                $resource_titledeed->QuantityForUpgrade = abs($value);
            }
            $resource_titledeed->create();
            
        }
    } else {
        //Vanlig resurs
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
    }
    $balance_upgrade = $resource->countBalance($larp)-$resource->countUpgrade($larp);
    echo $titledeed->calculateResult()." ".$resource->countBalance($larp)." ".$resource->countNumberOfCards($larp)." ".$balance_upgrade;

}
