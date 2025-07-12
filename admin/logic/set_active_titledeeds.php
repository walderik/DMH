<?php
include_once '../header.php';

$titledeeds = Titledeed::allByCampaign($current_larp, true);
foreach ($titledeeds as $titledeed) {
    if ($titledeed->hasRegisteredOwners($current_larp)) $titledeed->IsInUse = 1;
    else $titledeed->IsInUse = 0;
    $titledeed->update();
}

if (isset($_SERVER['HTTP_REFERER'])) {
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}

header('Location: ../titledeed_admin.php');