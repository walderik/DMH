<?php
include_once 'header.php';

// get the parameters from URL
$intrigueActorId = $_REQUEST["intrigueActorId"];
$text = str_replace("<br />", "\n", $_REQUEST["text"]);


if (empty($intrigueActorId)) {
    return;
}

$intrigueActor = IntrigueActor::loadById($intrigueActorId);
$type=$_REQUEST["type"];
if ($type=='intrigue') $intrigueActor->IntrigueText=$text;
elseif ($type=='off') $intrigueActor->OffInfo=$text;
elseif ($type=='notes') $intrigueActor->OrganizerNotes=$text;
else $intrigueActor->OrganizerNotes.=" Type: $type ";
$intrigueActor->update();

