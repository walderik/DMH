<?php
include_once 'header.php';

// get the parameters from URL
$intrigueActorId = $_REQUEST["intrigueActorId"];
$text = str_replace("<br />", "\n", $_REQUEST["text"]);


if (empty($intrigueActorId)) {
    return;
}

$intrigueActor = IntrigueActor::loadById($intrigueActorId);
$intrigueActor->IntrigueText=$text;
$intrigueActor->update();

