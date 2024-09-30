<?php
global $root, $current_user;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

require 'random_name.php';
require 'lorem_ipsum.php';

//Ifthe user isnt admin it may not see these pages
if (!AccessControl::hasAccessOther($current_user->Id, AccessControl::ADMIN)) {
    header('Location: ../../participant/index.php');
    exit;
}

if (!Dbh::isLocal()) exit;

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    echo "Anonymiserar data i databasen...<br>";
    
    $images = Image::all();
    $firstImageId = reset($images)->Id;
    
    
    $persons = Person::all();
    
    foreach($persons as $person) {
        $person->Email = "me@privacy.net";
        $person->EmergencyContact = RandomName::getName()." 070-00 00 00";
        $person->HealthComment = "";
        $person->HousingComment = "";
        $person->Name = RandomName::getName();
        $person->OtherInformation = "";
        $person->PhoneNumber = "070-00 00 00";
        $person->SocialSecurityNumber = rand(1965, 2023)."0101-0000";
        $person->update();
    }
    
    $roles = Role::all();
    foreach ($roles as $role) {
        if (isset($role->ImageId) && $role->ImageId != $firstImageId) {
            $role->ImageId = $firstImageId;
        }
        $role->CharactersWithRelations = "-";
        $role->DarkSecret = "-";
        $role->DarkSecretIntrigueIdeas = "-";
        $role->Description = LoremIpsum::getText(strlen($role->Description));
        $role->DescriptionForGroup = LoremIpsum::getText(strlen($role->DescriptionForGroup));
        $role->DescriptionForOthers = LoremIpsum::getText(strlen($role->DescriptionForOthers));
        $role->Name = RandomName::getName();
        $role->OrganizerNotes = "";
        $role->IntrigueSuggestions = LoremIpsum::getText(strlen($role->IntrigueSuggestions));
        $role->OtherInformation = "";
        $role->PreviousLarps = LoremIpsum::getText(strlen($role->PreviousLarps));
        $role->update();
    }
    
    $groups = Group::all();
    $groupNames = RandomName::getGroupNames(count($groups));
    foreach($groups as $i => $group) {
        if (isset($group->ImageId)) $group->ImageId = $firstImageId;
        $group->Description = LoremIpsum::getText(strlen($group->Description));
        $group->DescriptionForOthers = LoremIpsum::getText(strlen($group->DescriptionForOthers));
        $group->Enemies = LoremIpsum::getText(strlen($group->Enemies));
        $group->Friends = LoremIpsum::getText(strlen($group->Friends));
        $group->OrganizerNotes = "";
        $group->Name = $groupNames[$i];
        $group->OtherInformation = "";
        $group->update();
    }
    
    $intrigues = Intrigue::all();
    foreach ($intrigues as $intrigue) {
        $intrigue->Notes = LoremIpsum::getText(strlen($intrigue->Notes));
        $intrigue->update();
    }
    
    
    $intrigueActors = IntrigueActor::all();
    foreach ($intrigueActors as $intrigueActor) {
        $intrigueActor->IntrigueText = LoremIpsum::getText(strlen($intrigueActor->IntrigueText));
        $intrigueActor->OffInfo = LoremIpsum::getText(strlen($intrigueActor->OffInfo));
        $intrigueActor->WhatHappened = LoremIpsum::getText(strlen($intrigueActor->WhatHappened));
        $intrigueActor->update();
    }
    
    $letters = Letter::all();
    foreach ($letters as $letter) {
        $letter->Message = LoremIpsum::getText(strlen($letter->Message));
        $letter->OrganizerNotes = "";
        $letter->update();
    }
    
    $telegrams = Telegram::all();
    foreach ($telegrams as $telegram) {
        $telegram->Message = LoremIpsum::getText(strlen($telegram->Message));
        $telegram->OrganizerNotes = "";
        $telegram->update();
    }
    
    $rumours = Rumour::all();
    foreach($rumours as $rumour) {
        $rumour->Text = LoremIpsum::getText(strlen($rumour->Text));
        $rumour->Notes = "";
        $rumour->update();
    }
    
    $houses = House::all();
    foreach($houses as $house) {
        if (!empty($house->ImageId)) {
            $house->ImageId = $firstImageId;
            $house->update();
        }
    }
    
    $props = Prop::all();
    foreach($props as $prop) {
        if (!empty($prop->ImageId)) {
            $prop->ImageId = $firstImageId;
            $prop->update();
        }
    }
    
    $npcs = NPC::all();
    foreach($npcs as $npc) {
        if (!empty($npc->ImageId)) {
            $npc->ImageId = $firstImageId;
            $npc->update();
        }
    }
    
    $bookeepings = Bookkeeping::all();
    foreach($bookeepings as $bookeeping) {
        if (!empty($bookeeping->ImageId)) {
            $bookeeping->ImageId = $firstImageId;
            $bookeeping->update();
        }

    }
    
    $resources = Resource::all();
    foreach($resources as $resource) {
        if (!empty($resource->ImageId)) {
            $resource->ImageId = $firstImageId;
            $resource->update();
        }
        
    }
    
    foreach($images as $image) {
        if ($image->Id != $firstImageId) Image::delete($image->Id);
    }

}


// (A) SAVE IMAGE INTO DATABASE
if (isset($_FILES["upload"])) {
    
    $images = Image::all();
    $image = reset($images);
    
        $image->file_name = "anonym";
        $image->file_mime = mime_content_type($_FILES["upload"]["tmp_name"]);
        $image->file_data = file_get_contents($_FILES["upload"]["tmp_name"]);
        $image->Photographer = "";
        $image->update();
        
        
        
        header('Location: ../index.php?message=image_uploaded');
        exit;
}



//include 'navigation.php';
?>


	<div class="content">
		<h1>Ladda upp bild för anonymisering </h1>


    	<form method="post" enctype="multipart/form-data">
          	<input type="file" name="upload" required><br><br>

          	<br><br>
          	<input type="submit" name="submit" value="Ladda upp bild">
          	<p><strong>OBS:</strong> Bara .jpg, .jpeg, .gif, .png bilder är tillåtna och max storlek 0.5 MB.</p>
        </form>
    </div>
</body>
</html>
  


    
