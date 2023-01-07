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
    }
}
