<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require $root . '/includes/init.php';

//Ifthe user isnt admin it may not see these pages
if (!isset($_SESSION['admin'])) {
    header('Location: ../../participant/index.php');
    exit;
}




populateAll();


function populateAll() {
    echo "Börjar lägga in data<br>";
    global $current_larp;
    populateCampaign();
    
    $current_larp = createDMH2023();

      
    
    populateHouses();
    populateExperience();
    populateHousingRequest();
    populateIntrigueType();
    populateLarperType();
    populateNormalAllergyTypes();
    populateOffictialType();
    populatePlaceOfResidence();
    populateTypeOfFood();
    populateWealth();
    echo "Klar<br>";
}


function populateCampaign() {
    if (sizeof(Campaign::all())>0) {
        return;
    }
    
    $campaign = Campaign::newWithDefault();
    $campaign->Name = "Död mans hand";
    $campaign->Abbreviation = "DMH";
    $campaign->Description = "";
    $campaign->Icon = "dmh.ico";
    $campaign->Homepage = "https://dmh.berghemsvanner.se/";
    $campaign->Email = "dmh@berghemsvanner.se";
    $campaign->Bankaccount = "Swedbank, Clearingsnr 8417-8, Kontonr 934 505 367-3";
    $campaign->MinimumAge = 0;
    $campaign->MinimumAgeWithoutGuardian = 18;
    $campaign->create();
    
    echo "Skapade " . sizeof(Campaign::all()) . " kampanjer<br>";
    
}

function createDMH2023() {
    if (sizeof(LARP::all())>0) {
        return;
    }
    $campaign = Campaign::loadByAbbreviation("DMH");
    $larp = LARP::newWithDefault();
    $larp->Name = "Död mans hand 2023";
    $larp->MaxParticipants = 100;
    $larp->TagLine = "";
    $larp->StartDate = "2023-09-15 18:00";
    $larp->EndDate = "2023-09-17 12:00";
    $larp->LatestRegistrationDate = "2023-07-15";
    
    $larp->StartTimeLARPTime = "1868-09-15 18:00";
    $larp->EndTimeLARPTime = "1868-09-17 12:00";
    
    $larp->CampaignId = $campaign->Id;
    $larp->create();
    echo "Skapade " . sizeof(LARP::all()) . " lajv<br>";    return $larp;    
}

function populateHouses() {
    if (sizeof(House::all())>0) {
        return;
    }
    
    $house = House::newWithDefault();
    $house->Name = "Värdshuset";
    $house->PositionInVillage = "Centralt i byn, vid vägen";
    $house->Description = "En större sal med långbord och bänkar, bardisk för betalning/ servering och en eldstad som är godkänd för eldning. Det finns även en överbyggd veranda med fönsterluckor där det står långbord och bänkar. Sovplatser på loftet för kökspersonal. För serveringspersonalen finns det en gång och dörr till skafferiet och köksdelen på baksidan.";
    $house->NumberOfBeds = "3";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Grims hus";
    $house->PositionInVillage = "Längst bort i byn på vänster sida. Efter värdshusets kök, nedanför Oktagonen.";
    $house->Description = "Våningssäng med två bäddar, plats för fler sängar eller bord och stolar.";
    $house->NumberOfBeds = "2-3";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Galtåsen";
    $house->PositionInVillage = "I utkanten av byn, framför “kampringen”.";
    $house->Description = "Stort tvåvåningshus med egen eldplats utanför. Två rum på ovanvåningen varav ett med 3 sängar och ett omöblerat. Balkong utanför sovrummet. Första våningen är samlingsrum med ett stort matbord. Utanför finns även ett bord med tak för servering, disk eller liknande.";
    $house->NumberOfBeds = "3 sängar + 5 golvplatser";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Utsikten";
    $house->PositionInVillage = "Längst in i byn längst åt höger med utsikt över dalen.";
    $house->Description = "Ett ganska långt och högt hus. Ett stort bord med sittplatser som fungerar bra vid möten med mindre grupper, vid hantverk eller som matplats. Uppbyggd sovdel och loft där det också går att sova.";
    $house->NumberOfBeds = "6";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Insikten";
    $house->PositionInVillage = "Längst in i byn på höger sida, i skogskanten";    
    $house->Description = "Byggår 2021, mindre hus som passar till exempel en familj med mindre barn.";
    $house->NumberOfBeds = "2-3";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Snokhuset";
    $house->PositionInVillage = "Långt in i byn på höger sida, nära köket.";
    $house->Description = "Enkelt boende i fasta sängar, bra för en grupp som spelar ihop.";
    $house->NumberOfBeds = "4-6";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Walbergastugan";
    $house->PositionInVillage = "Mittemot värdshuset";
    $house->Description = "Stort välbyggt boningshus något upphöjt från marken. Det är 24 kvadratmeter stort. Huset har två stora bord, bänkar, fasta förvaringskistor, hyllor, garderob, köksavdelning med diskbänk och diskbaljor, tre fönsterluckor och en mysig altan.
Spisen är i nuläget tyvärr demonterad i väntan på nya brandsäkerhetsbestämmelser.";
    $house->NumberOfBeds = "Fyra, 120 cm breda, plats för 8 personer.";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Bäckabo";
    $house->PositionInVillage = "Huset ligger snett fram till vänster i förhållande till värdshuset.";
    $house->Description = "Ett ganska stort hus. Stort utrymme för matbord och bänkar. Uteplats med tak på framsidan av huset med bord och bänkar.";
    $house->NumberOfBeds = "6-7";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Liljebacken";
    $house->PositionInVillage = "Vid vägen, centralt i byn.";
    $house->Description = "Liten stuga med flexibel inredning. Byggnadsår 2018.";
    $house->NumberOfBeds = "2-5";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Baggen";
    $house->PositionInVillage = "Vid vägen, centralt i byn.";
    $house->Description = "Liten stuga med två fasta 90 cm breda sängar i vinkel. Byggnadsår 2018. Låg tröskel med dörr mot vägen, gör det lätt att komma in. Fönsterlucka mot vägen går att fälla ner för ljusinsläpp eller som reception/ telegraf/ handel eller liknande.";
    $house->NumberOfBeds = "2-5";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Nolänga";
    $house->PositionInVillage = "På höger sida en bit in i byn";
    $house->Description = "Ett litet hus som ligger centralt i byn, vid vägen och nära värdshuset. Byggår 2018. Låg tröskel gör det lätt att komma in, stora fönster med luckor som går att öppna på båda långsidorna. Bord och bänk i stugan. Mysigt och ombonat. Två fasta sängar, över varandra.";
    $house->NumberOfBeds = "2-4";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Draknästet";
    $house->PositionInVillage = "På höger sida en bit in i byn, bredvid stenröset.";
    $house->Description = "Ett litet hus med ett rum, gott om plats, högt i tak och flexibelt eftersom det inte är så många möbler i huset.";
    $house->NumberOfBeds = "3-5";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Kottebo";
    $house->PositionInVillage = "Till höger om vägen, nere i slänten.";
    $house->Description = "Ett litet hus som det fungerar att sova, äta och bo i. Två fasta sängar, över varandra. En liten sittplats under tak på ena kortsidan.";
    $house->NumberOfBeds = "2-4";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Vindhem";
    $house->PositionInVillage = "I skogsbrynet nära brunnen, till höger om vägen, nere i slänten.";
    $house->Description = "Vindhem är ett renodlat boningshus. Det finns ett rum att umgås i och ett rum att sova i. Två fasta våningssängar.";
    $house->NumberOfBeds = "4-6";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Korpen";
    $house->PositionInVillage = "Huset ligger ovanför brunnen, i skogsbrynet till höger om vägen. Till huset hör en fast eldplats på framsidan.";
    $house->Description = "Byggt efter en modell som kallas båthus (takets utformning). Ett ganska stort hus med fasta sängar i två våningar.";
    $house->NumberOfBeds = "3-5";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Askebo";
    $house->PositionInVillage = "Andra huset på höger sida efter vändplanen. Gården är granne till Langebro gård.";
    $house->Description = "Avstängt för renovering";
    $house->NumberOfBeds = "0";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Langebro gård";
    $house->PositionInVillage = "Gården ligger på höger sida efter vändplanen, har flera byggnader.";
    $house->Description = "Langebro är en befäst försvarsanläggning med palissad och vakttorn. Gården består av flera byggnader: en lång isolerad tredelad huvudbyggnad, eldplats, fasta bord/bänkar under tak, eldgrop, matlagningshus, härbre på stolpar och jordkällare. Husen är av Nytegstyp i konstruktionen. I hagen utanför gården finns det även plats för ett större tält eller flera små. Gården passar därför bra som bas till en större grupp.";
    $house->NumberOfBeds = "7-12";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Drakahall";
    $house->PositionInVillage = "Direkt till vänster uppe på en höjd när man kommer in i byn.";
    $house->Description = "Ett 1100-tals hus i två våningar med balkong. Nedre våningen är matrum och förråd med långbord, bänkar och förvaringskistor. På övre plan är det ingen inredning men gott om plats för madrasser.";
    $house->NumberOfBeds = "10-15";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Valtorp";
    $house->PositionInVillage = "På vänster sida av vägen en bit in i byn.";
    $house->Description = "Valtorp har två våningar. Det är inte ståhöjd på hela övervåningen. Det finns bord och bänkar men annars är huset tomt på saker.";
    $house->NumberOfBeds = "6 st 120 cm sängar + golvyta";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Farevi";
    $house->PositionInVillage = "På vänster sida av vägen, centralt i byn.";
    $house->Description = "Liten, enkel stuga med lös inredning. Plats för några sängplatser och ett bord.";
    $house->NumberOfBeds = "2-3";
    $house->create();
    
    $house = House::newWithDefault();
    $house->Name = "Skarven";
    $house->PositionInVillage = "Mitt i byn.";
    $house->Description = "Ett skrytbygge som inte går att bo i. ;).";
    $house->NumberOfBeds = "0";
    $house->create();
    
    echo "Skapade " . sizeof(House::all()) . " hus<br>";
}

function populateWealth() {
    if (sizeof(Wealth::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    
    $wealth = Wealth::newWithDefault();
    $wealth->Name = "1. Urfattig";
    $wealth->Description = "Klarar oftast av att betala mat för dagen";
    $wealth->CampaignId = $campaign->Id;
    $wealth->create();
 
    $wealth = Wealth::newWithDefault();
    $wealth->Name = "2. Ganska fattig";
    $wealth->Description = "Lever inte gott men svälter inte";
    $wealth->CampaignId = $campaign->Id;
    $wealth->create();
    
    $wealth = Wealth::newWithDefault();
    $wealth->Name = "3. Medelklass";
    $wealth->Description = "Lite lagomt rik.";
    $wealth->CampaignId = $campaign->Id;
    $wealth->create();
    
    $wealth = Wealth::newWithDefault();
    $wealth->Name = "4. Rik";
    $wealth->Description = "Har råd med det mesta man kan önska sig";
    $wealth->CampaignId = $campaign->Id;
    $wealth->create();
    
    $wealth = Wealth::newWithDefault();
    $wealth->Name = "5. Jätterik";
    $wealth->Description = "Kan gödsla med pengar";
    $wealth->CampaignId = $campaign->Id;
    $wealth->create();
    echo "Skapade " . sizeof(Wealth::all()) . " rikedom<br>";
}

function populateTypeOfFood() {
    
    if (sizeof(TypeOfFood::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    $food = TypeOfFood::newWithDefault();
    $food->Name = "Kött";
    $food->Description = "Mat som innehåller kött";
    $food->CampaignId = $campaign->Id;
    $food->create();
    
    $food = TypeOfFood::newWithDefault();
    $food->Name = "Vegetariskt";
    $food->Description = "Inget kött";
    $food->CampaignId = $campaign->Id;
    $food->create();
    
    $food = TypeOfFood::newWithDefault();
    $food->Name = "Veganskt";
    $food->Description = "Innehåller inget från djur";
    $food->CampaignId = $campaign->Id;
    $food->create();
    echo "Skapade " . sizeof(TypeOfFood::all()) . " typer av mat<br>";
}

function populatePlaceOfResidence() {
    if (sizeof(PlaceOfResidence::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Slow River";
    $place->Description = "Här på plats";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Junk City";
    $place->Description = "Stan i närheten";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
        $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Denver";
    $place->Description = "Storstan";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Övriga Minnekapi";
    $place->Description = "Övriga delstaten";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Colorado";
    $place->Description = "Delstaten";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "USA";
    $place->Description = "USA, förrutom Minnekapi";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Annan plats";
    $place->Description = "Övriga världen";
    $place->CampaignId = $campaign->Id;
    $place->create();
    
    $place = PlaceOfResidence::newWithDefault();
    $place->Name = "Kringflackande";
    $place->Description = "(t ex circusfolk, vissa indian-stammar)";
    $place->CampaignId = $campaign->Id;
    $place->create();
    echo "Skapade " . sizeof(PlaceOfResidence::all()) . " boplatser<br>";
}

function populateNormalAllergyTypes() {
    if (sizeof(NormalAllergyType::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    $allergy = NormalAllergyType::newWithDefault();
    $allergy->Name = "Gluten";
    $allergy->Description = "Glutenprotein";
    $allergy->CampaignId = $campaign->Id;
    $allergy->create();
    
    /*
    $allergy = NormalAllergyType::newWithDefault();
    $allergy->Name = "Ägg";
    $allergy->Description = "Äggviteprotein";
    $allergy->CampaignId = $campaign->Id;
    $allergy->create();
    
    $allergy = NormalAllergyType::newWithDefault();
    $allergy->Name = "Nötter";
    $allergy->Description = "Nötter, mandlar eller jordnötter";
    $allergy->CampaignId = $campaign->Id;
    $allergy->create();
    */
    $allergy = NormalAllergyType::newWithDefault();
    $allergy->Name = "Laktos";
    $allergy->Description = "Laktossocker (inte samma sak som mjölkprotein)";
    $allergy->CampaignId = $campaign->Id;
    $allergy->create();
    /*
    $allergy = NormalAllergyType::newWithDefault();
    $allergy->Name = "Lök";
    $allergy->Description = "Gullök, vitlök och purjo";
    $allergy->CampaignId = $campaign->Id;
    $allergy->create();
    */
    echo "Skapade " . sizeof(NormalAllergyType::all()) . " allergier<br>";
}

function populateLarperType() {
    if (sizeof(LarperType::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    
    $larpertype = LarperType::newWithDefault();
    $larpertype->Name = "Myslajvare";
    $larpertype->Description = "Jag vill bara sitta vid elden och dricka te och småprata om minnen från förr. Jag får inga intriger och är inte inblandad i någon annans intriger. Myslajvare får heller ingen handel och blir troligen varken fattigare eller rikare under lajvet.(rekommenderas inte för nybörjare eller barn.)";
    $larpertype->CampaignId = $campaign->Id;
    $larpertype->create();
    
    $larpertype = LarperType::newWithDefault();
    $larpertype->Name = "Passiv lajvare";
    $larpertype->Description = "Jag vill inte ha en viktig roll i lajvet. Jag åker mest för stämningen och för att interagera med min grupp eller övriga karaktärer. Jag vill inte ha några egna intriger mer än lite svaller om andra karaktärer som jag kan sprida.";
    $larpertype->CampaignId = $campaign->Id;
    $larpertype->create();
    
    $larpertype = LarperType::newWithDefault();
    $larpertype->Name = "Karaktärslajvare";
    $larpertype->Description = "Min karaktär är allt. Jag har gärna intriger, men de får på intet sätt gå emot det jag skrivit om mig i min bakgrund.";
    $larpertype->CampaignId = $campaign->Id;
    $larpertype->create();
    
        $larpertype = LarperType::newWithDefault();
    $larpertype->Name = "Aktiv lajvare";
    $larpertype->Description = "Jag älskar intriger, men jag älskar också min karaktär. Jag kan tänka mig att tumma lite på min karaktär om det innebär att jag får mer intriger.";
    $larpertype->CampaignId = $campaign->Id;
    $larpertype->create();
    
    $larpertype = LarperType::newWithDefault();
    $larpertype->Name = "Action-lajvare";
    $larpertype->Description = "Jag vill gärna maxa min upplevelser på lajvet. Jag ser därför gärna att arrangörerna skriver en roll åt mig som passar den typer av intriger som jag gillar.";
    $larpertype->CampaignId = $campaign->Id;
    $larpertype->create();
    echo "Skapade " . sizeof(LarperType::all()) . " typ av lajvare<br>";
}

function populateOffictialType() {
    if (sizeof(OfficialType::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    $officialtype = OfficialType::newWithDefault();
    $officialtype->Name = "Kök";
    $officialtype->Description = "Jobbar i köket stora delar av lajvet. Jobbet innebär bland annat skära grönsaker, diska, elda, servera mat till deltagare och laga mat.";
    $officialtype->CampaignId = $campaign->Id;
    $officialtype->create();

    $officialtype = OfficialType::newWithDefault();
    $officialtype->Name = "Praktiskt";
    $officialtype->Description = "Hjälpa till med småsaker under lajvet, tex städa dass, tända lyktor osv.";
    $officialtype->CampaignId = $campaign->Id;
    $officialtype->create();
    
    $officialtype = OfficialType::newWithDefault();
    $officialtype->Name = "Trygghetsvärd";
    $officialtype->Description = "Tar hand om folk som behöver stöd en stund.";
    $officialtype->CampaignId = $campaign->Id;
    $officialtype->create();
    
    $officialtype = OfficialType::newWithDefault();
    $officialtype->Name = "Sjukvårdare";
    $officialtype->Description = "Släpper allt och springer vid 'Skarp skada'. Har sjukvårdsutbildning.";
    $officialtype->CampaignId = $campaign->Id;
    $officialtype->create();
    
    $officialtype = OfficialType::newWithDefault();
    $officialtype->Name = "Efter lajvet";
    $officialtype->Description = "Stannar kvar på området tills det är rent.";
    $officialtype->CampaignId = $campaign->Id;
    $officialtype->create();
    echo "Skapade " . sizeof(OfficialType::all()) . " typ av funktionärer<br>";
}

function populateIntrigueType() {
    if (sizeof(IntrigueType::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Lägerhäng";
    $intriguetype->Description = "Det bästa på lajvet är att sitta och umgås med folk. Du kommer på något sätt att se till att det blir av. Troligen får du ingen skriven intrig kring det här, men det vore praktiskt att kopplas ihop med andra lägerhängare.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Sociala tillställningar";
    $intriguetype->Description = "Lite som lägerhäng, men mer för en utvald grupp. Kanske alla finare damer borde samlas? Eller vill du gå med i ett hemligt sällskap för personer med inflytande? Varför organiseras inga poesi-salonger i byn?";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Relationer";
    $intriguetype->Description = "Din karaktär vill hitta en partner, har problem i sin nuvarande relation eller har en närstående som borde giftas bort. Vill den hitta en partner kan vi kanske hitta någon annan på lajvet att para ihop med. Eller vill du hellre para ihop andra?";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Misär";
    $intriguetype->Description = "Du vill ha det svårt på lajvet. Bli sjuk, fattig och fråntagen allt du äger är inte uteslutet. Skilsmässa? Javisst! Karaktären tillhör säkert en utsatt grupp. Kanske har du gjort något dumt och har ångest för det.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "By-liv";
    $intriguetype->Description = "Har något satt upp ett stängsel så dina kor inte når vattnet? Taggtråd, vem gillar taggtråd? Varför badar någon naken i indian-ån? Det är sällan lugn och ro mellan grannar.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Handel";
    $intriguetype->Description = "Alla som har angett rikedom 3 eller mer får i regel lite handel. Men väljer du aktivt den här typen av intrig (oavsett hur rik du är) innebär det att du för köpa och sälja saker och land inom lajvets system för handel. Det betyder alltsåu att du får mer intensiv handel än andra, men lyckas du kan du bli riktigt rik.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Leta guld";
    $intriguetype->Description = "Alla vet det finns guld runt Slow River. Du kommer att tillbringa en del av lajvet att gå runt i skogen runt området och leta efter guldklimpar inom lajvets system för inmitningar. Det kan bli blött och kan vara tråkigt om du inte hittar något guld. Men hittar du guld blir du säkert jätterik. Om ingen rånar dig.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Poker";
    $intriguetype->Description = "Slow River är känd för sina pokerturneringar och du vill delta i en eller flera. Blir det inget poker så kanske du själv startar en. Är du falskspelare? Är du en känd korthaj? Poker är inte ofarligt.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Politik";
    $intriguetype->Description = "Du vill bli vald till Borgmästare, domare, åklagare, skattmas eller någon annan post där man får bestämma och få betalt för det. Varför finns det inget statsråd i Slow River? Ska borgmästare styra eller vara en representant för rådet? Alternativt kan du ha en stark politisk åsikt och försöker omforma världen till den (ex anarkist, rojalist, populist, illuminist)";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Religion";
    $intriguetype->Description = "Din karaktär är religiös och vill hitta fler personer med samma tro. Den vill också genomföra religiösa ceremonier. Är den i en religiös struktur så har överordnade säkert krav på karaktären också. Är karaktären en religiös ledare så kanske flocken inte alltid är så lätt att leda. Ska religiösa motståndare motarbetas?";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Deckargåtor";
    $intriguetype->Description = "Du är troligen inte kriminell. Ett brott, gärna ett mord, har begåtts. Av någon orsak vill du lösa det. Kanske bara för att du är olidligt nyfiken. Men se upp! Om brottslingen får reda på att du är den på spåren kanske du behöver tystas.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Kriminalitet";
    $intriguetype->Description = "Din karaktär är kriminell. Den kan ha gjort kriminella handlingar som påverkar lajvet. Att göra brottsliga handlingar under lajvet är inte heller uteslutet. Var också beredd på att det kan innebära strid och vara farligt.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Ond bråd död";
    $intriguetype->Description = "Din karaktär har begått ett mord eller dråp i det förflutna (som kan komma att uppdagas under lajvet) eller du kan tänka dig att begå ett liknande brott under lajvet som en del av din intrig.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    
    $intriguetype = IntrigueType::newWithDefault();
    $intriguetype->Name = "Strid";
    $intriguetype->Description = "Du vill skjuta med dina vapen och vill sättas i situationer där handgemäng, dueller och eldstrider är logiska lösningar. Självklart är du också beredd på att få byta karaktär under lajvet.";
    $intriguetype->CampaignId = $campaign->Id;
    $intriguetype->create();
    echo "Skapade " . sizeof(IntrigueType::all()) . " typ av intriger<br>";
 }

function populateHousingRequest() {
    if (sizeof(HousingRequest::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    $housing = HousingRequest::newWithDefault();
    $housing->Name = "Hus i byn";
    $housing->Description = "Ett hus i byn.";
    $housing->CampaignId = $campaign->Id;
    $housing->create();

    $housing = HousingRequest::newWithDefault();
    $housing->Name = "Eget hus i byn";
    $housing->Description = "Vi har en i vår grupp som förvaltar ett hus som gruppen vill använda.";
    $housing->CampaignId = $campaign->Id;
    $housing->create();

    $housing = HousingRequest::newWithDefault();
    $housing->Name = "Tält i byn";
    $housing->Description = "Ett westerntält mitt i byn.";
    $housing->CampaignId = $campaign->Id;
    $housing->create();
    
    $housing = HousingRequest::newWithDefault();
    $housing->Name = "In-läger i skogen";
    $housing->Description = "Ett westernläger utanför byn.";
    $housing->CampaignId = $campaign->Id;
    $housing->create();
    
    $housing = HousingRequest::newWithDefault();
    $housing->Name = "Off-läger";
    $housing->Description = "Bor på offen (en bit från byn). Här går det bra att bo i ett modern tält.";
    $housing->CampaignId = $campaign->Id;
    $housing->create();
    
    $housing = HousingRequest::newWithDefault();
    $housing->Name = "Kommer inte bo på området";
    $housing->Description = "Bor någon annastans.";
    $housing->CampaignId = $campaign->Id;
    $housing->create();
    echo "Skapade " . sizeof(HousingRequest::all()) . " boendeönskemål<br>";
}

function populateExperience() {
    if (sizeof(Experience::all())>0) {
        return;
    }
    
    //DMH
    $campaign = Campaign::loadByAbbreviation("DMH");
    
    $experience = Experience::newWithDefault();
    $experience->Name = "Nybörjare";
    $experience->Description = "Jag har aldrig lajvat förr.";
    $experience->CampaignId = $campaign->Id;
    $experience->create();
    
    $experience = Experience::newWithDefault();
    $experience->Name = "Ganska ny";
    $experience->Description = "Har varit på minst ett lajv.";
    $experience->CampaignId = $campaign->Id;
    $experience->create();
    
    $experience = Experience::newWithDefault();
    $experience->Name = "Erfaren";
    $experience->Description = "Har varit på flera lajv.";
    $experience->CampaignId = $campaign->Id;
    $experience->create();
    
    $experience = Experience::newWithDefault();
    $experience->Name = "Väldigt erfaren";
    $experience->Description = "Har varit på fler lajv än jag kommer ihåg.";
    $experience->CampaignId = $campaign->Id;
    $experience->create();
    echo "Skapade " . sizeof(Experience::all()) . " erfarenhetsnivåer<br>";
}
