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
        case "council" : return "Byråd";
        case "guard" : return "Markvakt";
        case "religon" : return "Religion";
        case "advertismenttypes" : return "Annonstyper";
        case "titledeedplace" : return "Platser för verksamheter";
        case "grouptype" : return "Typ av grupp";
        case "shiptype" : return "Typ av skepp";
        case "colour" : return "Färg";
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
        case "council" : return "Council";
        case "guard" : return "Guard";
        case "religion" : return "Religion";
        case "advertismenttypes" : return "AdvertismentType";
        case "titledeedplace" : return "TitledeedPlace";
        case "grouptype" : return "GroupType";
        case "shiptype" : return "ShipType";
        case "colour" : return "Colour";
    }
}