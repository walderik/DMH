<?php

function getObjectName($name) {
    //Eftersom typen kommer in som indata vill jag ha en kontroll på att bara de jag tillåter kan visas
    switch ($name) {
        case "wealth" : return "Rikedom";
        case "typesoffood" : return "Matalternativ";
        case "placeofresidence" : return "Vad karaktärer/grupper bor"; 
        case "officialtypes" : return "Typ av funktionärer"; 
        case "normalallergytypes" : return "Vanliga allergier";
        case "larpertypes" : return "Typ av lajvare";
        case "intriguetypes" : return "Typ av intriger";
        case "housingrequests" : return "Boendeönskemål";
        case "experiences" : return "Erfarenhet";
        case "race" : return "Ras";
        case "abilities" : return "Förmågor";
        case "religon" : return "Religion";
        case "advertismenttypes" : return "Annonstyper";
        case "titledeedplace" : return "Platser för verksamheter";
        case "grouptype" : return "Typ av grupp";
        case "shiptype" : return "Typ av skepp";
        case "belief" : return "Hur troende";
        case "rolefunction" : return "Karaktärens funktion";
    }
}

function getObjectType($name) {
    //Eftersom typen kommer in som indata vill jag ha en kontroll på att bara de jag tillåter kan visas
    switch ($name) {
        case "wealth" : return "Wealth";
        case "typesoffood" : return "TypeOfFood";
        case "placeofresidence" : return "PlaceOfResidence";        
        case "officialtypes" : return "OfficialType";
        case "normalallergytypes" : return "NormalAllergyType";
        case "larpertypes" : return "LarperType";
        case "intriguetypes" : return "IntrigueType";
        case "housingrequests" : return "HousingRequest";
        case "experiences" : return "Experience";
        case "race" : return "Race";
        case "abilities" : return "Ability";
        case "religion" : return "Religion";
        case "advertismenttypes" : return "AdvertismentType";
        case "titledeedplace" : return "TitledeedPlace";
        case "grouptype" : return "GroupType";
        case "shiptype" : return "ShipType";
        case "belief" : return "Belief";
        case "rolefunction" : return "RoleFunction";
    }
}

function getAllTypesForRoles(Larp $larp) {
    $types = array();
    if (Wealth::isInUse($larp)) $types["Wealth"] = "Rikedom";
    if (PlaceOfResidence::isInUse($larp)) $types["PlaceOfResidence"] = "Vad karaktärer bor";
    if (LarperType::isInUse($larp)) $types["LarperType"] = "Typ av lajvare";
    if (IntrigueType::isInUse($larp)) $types["IntrigueType"] = "Typ av intriger";
    if (Race::isInUse($larp)) $types["Race"] = "Ras";
    if (Ability::isInUse($larp)) $types["Ability"] = "Förmågor";
    if (Religion::isInUse($larp)) $types["Religion"] = "Religion";
    if (Belief::isInUse($larp)) $types["Belief"] = "Hur troende";
    if (RoleFunction::isInUse($larp)) $types["RoleFunction"] = "Karaktärens funktion";
    
    return $types;
}

function getAllOptionsForRoles(Larp $larp) {
    $options = array();
    $types = getAllTypesForRoles($larp);
    foreach ($types as $typekey => $typeName) {
        $options[$typekey] = call_user_func($typekey . '::allForLarp', $larp);
    }
    
    
    return $options;
}
