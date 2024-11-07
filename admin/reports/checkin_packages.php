<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/pdf/report_tcpdf_pdf.php';

include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] != "GET") {
    header('Location: ../../admin/index.php');
    exit;
}

$name = 'Paketering';

$pdf = new Report_TCP_PDF();

$pdf->init($current_user->Name, $name, $current_larp->Name, false);


function cmp($a, $b)
{
    if ($a->Name == $b->Name) {
        return 0;
    }
    return ($a->Name < $b->Name) ? -1 : 1;
}

$roles = Role::getAllMainRoles($current_larp, false);
usort($roles, "cmp");
$currency = $current_larp->getCampaign()->Currency;
$rows = array();
$header = array("Namn", "Ska ha                                                                                                                        ");
foreach ($roles as $role) {
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    $checkin_letters = $role->getAllCheckinLetters($current_larp);
    $checkin_telegrams = $role->getAllCheckinTelegrams($current_larp);
    $checkin_props = $role->getAllCheckinProps($current_larp);
    
    $package = "";
    
    //Pengar
    $start_money = (isset($larp_role->StartingMoney)) ? $larp_role->StartingMoney : '0';
    $package .= "$start_money $currency\n";
    
    
    //Verksamheter
    $titlededsArr = array();
    $titledeeds = Titledeed::getAllForRole($role);
    foreach ($titledeeds as $titledeed) {
        if ($titledeed->isInUse() && $titledeed->IsFirstOwnerRole($role)) {
           $titlededsArr[] = "  $titledeed->Name";
        }
    }
    
    if (!empty($titlededsArr)) $package .= "Verksamheter:\n".implode("\n", $titlededsArr)."\n";
    
    
    
    $docuumentsArr = array();
    //Intrighandouts
    $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
    foreach ($intrigues as $intrigue) {
        $intrgueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
        $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
        foreach($intrigue_Pdfs as $intrigue_Pdf) {
            $docuumentsArr[] = "  $intrigue_Pdf->Filename";
        }
    }
    
    
    //Brev
    foreach($checkin_letters as $checkin_letter) {
        $letter = $checkin_letter->getIntrigueLetter()->getLetter();   
        $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
    }
    
    //Telegram
    foreach($checkin_telegrams as $checkin_telegram) {
        $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
        $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
    }
    
    //Dokument från grupperingar
    $subdivisions = Subdivision::allForRole($role);
    foreach ($subdivisions as $subdivision) {
        //Intrighandouts
        $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
        foreach ($intrigues as $intrigue) {
            $intrgueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
            $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
            foreach($intrigue_Pdfs as $intrigue_Pdf) {
                $docuumentsArr[] = "  $intrigue_Pdf->Filename";
            }

            $checkin_props_subdivision = $subdivision->getAllCheckinProps($current_larp);
            $checkin_props = array_merge($checkin_props ,$checkin_props_subdivision);
            
            if ($subdivision->IsFirstRole($role, $current_larp)) {

                $checkin_letters_subdivision = $subdivision->getAllCheckinLetters($current_larp);
                $checkin_telegrams_subdivision = $subdivision->getAllCheckinTelegrams($current_larp);
                

                
                //Brev
                foreach($checkin_letters_subdivision as $checkin_letter) {
                    $letter = $checkin_letter->getIntrigueLetter()->getLetter();
                    $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
                }
                
                //Telegram
                foreach($checkin_telegrams_subdivision as $checkin_telegram) {
                    $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
                    $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
                }
            } 
            
        }
        
        
        
    }
    
    
    
    
    if (!empty($docuumentsArr)) $package .= "Dokument:\n".implode("\n", array_unique($docuumentsArr))."\n";

    //Props
    $props_txt_Arr = array();
    foreach($checkin_props as $checkin_prop) $props_txt_Arr[] = $checkin_prop->getIntrigueProp()->getProp()->Name;
    if (!empty($props_txt_Arr)) $package .= "Rekvisita: ". implode(", ", $props_txt_Arr);

    
    $rows[] = array($role->Name, $package);
}

//Sidokaraktärer
$roles = Role::getAllNotMainRoles($current_larp, false);
usort($roles, "cmp");

$currency = $current_larp->getCampaign()->Currency;
foreach ($roles as $role) {
    $larp_role = LARP_Role::loadByIds($role->Id, $current_larp->Id);
    $checkin_letters = $role->getAllCheckinLetters($current_larp);
    $checkin_telegrams = $role->getAllCheckinTelegrams($current_larp);
    $checkin_props = $role->getAllCheckinProps($current_larp);
    
    $package = "";
    
    //Pengar
    if (!empty($larp_role->StartingMoney)) $package .= "$larp_role->StartingMoney $currency\n";
    
    
    //Verksamheter
    $titlededsArr = array();
    $titledeeds = Titledeed::getAllForRole($role);
    foreach ($titledeeds as $titledeed) {
        if ($titledeed->isInUse() && $titledeed->IsFirstOwnerRole($role)) {
            $titlededsArr[] = "  $titledeed->Name";
        }
    }
    
    if (!empty($titlededsArr)) $package .= "Verksamheter:\n".implode("\n", $titlededsArr)."\n";
    
    
    
    $docuumentsArr = array();
    //Intrighandouts
    $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $current_larp->Id);
    foreach ($intrigues as $intrigue) {
        $intrgueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
        $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
        foreach($intrigue_Pdfs as $intrigue_Pdf) {
            $docuumentsArr[] = "  $intrigue_Pdf->Filename";
        }
    }
    
    
    //Brev
    foreach($checkin_letters as $checkin_letter) {
        $letter = $checkin_letter->getIntrigueLetter()->getLetter();
        $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
    }
    
    //Telegram
    foreach($checkin_telegrams as $checkin_telegram) {
        $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
        $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
    }
    
    //Dokument från grupperingar
    $subdivisions = Subdivision::allForRole($role);
    foreach ($subdivisions as $subdivision) {
        //Intrighandouts
        $intrigues = Intrigue::getAllIntriguesForSubdivision($subdivision->Id, $current_larp->Id);
        foreach ($intrigues as $intrigue) {
            $intrgueActor = IntrigueActor::getSubdivisionActorForIntrigue($intrigue, $subdivision);
            $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
            foreach($intrigue_Pdfs as $intrigue_Pdf) {
                $docuumentsArr[] = "  $intrigue_Pdf->Filename";
            }
            
            $checkin_props_subdivision = $subdivision->getAllCheckinProps($current_larp);
            $checkin_props = array_merge($checkin_props ,$checkin_props_subdivision);
            
            if ($subdivision->IsFirstRole($role, $current_larp)) {
                
                $checkin_letters_subdivision = $subdivision->getAllCheckinLetters($current_larp);
                $checkin_telegrams_subdivision = $subdivision->getAllCheckinTelegrams($current_larp);
                
                
                
                //Brev
                foreach($checkin_letters_subdivision as $checkin_letter) {
                    $letter = $checkin_letter->getIntrigueLetter()->getLetter();
                    $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
                }
                
                //Telegram
                foreach($checkin_telegrams_subdivision as $checkin_telegram) {
                    $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
                    $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
                }
            }
            
        }
        
        
        
    }
    
    
    
    
    if (!empty($docuumentsArr)) $package .= "Dokument:\n".implode("\n", array_unique($docuumentsArr))."\n";
    
    //Props
    $props_txt_Arr = array();
    foreach($checkin_props as $checkin_prop) $props_txt_Arr[] = $checkin_prop->getIntrigueProp()->getProp()->Name;
    if (!empty($props_txt_Arr)) $package .= "Rekvisita: ". implode(", ", array_unique($props_txt_Arr));
    
    if (!empty($package)) $rows[] = array($role->Name." (S)", $package);
}


// add a page
$pdf->AddPage();
// print table
$pdf->Table("Karaktärer ska ha vid lajvstart", $header, $rows);


$groups = Group::getAllRegistered($current_larp);
$rows = array();
$header = array("Namn", "Ska ha                                                                                                                        ");
foreach ($groups as $group) {
    $larp_group = LARP_Group::loadByIds($group->Id, $current_larp->Id);
    $checkin_letters = $group->getAllCheckinLetters($current_larp);
    $checkin_telegrams = $group->getAllCheckinTelegrams($current_larp);
    $checkin_props = $group->getAllCheckinProps($current_larp);
    
    $package = "";
    
    //Pengar
    $start_money = (isset($larp_group->StartingMoney)) ? $larp_group->StartingMoney : '0';
    $package .= "$start_money $currency\n";
    
    
    //Verksamheter
    $titlededsArr = array();
    $titledeeds = Titledeed::getAllForGroup($group);
    foreach ($titledeeds as $titledeed) {
        if ($titledeed->IsFirstOwnerGroup($group)) {
            $titlededsArr[] = "  $titledeed->Name";
        }
    }
    if (!empty($titlededsArr)) $package .= "Verksamheter:\n".implode("\n", $titlededsArr)."\n";
    
    
    
    $docuumentsArr = array();
    //Intrighandouts
    $intrigues = Intrigue::getAllIntriguesForGroup($group->Id, $current_larp->Id);
    foreach ($intrigues as $intrigue) {
        $intrgueActor = IntrigueActor::getGroupActorForIntrigue($intrigue, $group);
        $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
        foreach($intrigue_Pdfs as $intrigue_Pdf) {
            $docuumentsArr[] = "  $intrigue_Pdf->Filename";
        }
    }
    
    
    //Brev
    foreach($checkin_letters as $checkin_letter) {
        $letter = $checkin_letter->getIntrigueLetter()->getLetter();
        $docuumentsArr[] = "  Brev från: $letter->Signature till: $letter->Recipient";
    }
    
    //Telegram
    foreach($checkin_telegrams as $checkin_telegram) {
        $telegram = $checkin_telegram->getIntrigueTelegram()->getTelegram();
        $docuumentsArr[] = "  Telegram från: $telegram->Sender till: $telegram->Reciever";
    }
    if (!empty($docuumentsArr)) $package .= "Dokument:\n".implode("\n", $docuumentsArr)."\n";
    
    //Props
    $props_txt_Arr = array();
    foreach($checkin_props as $checkin_props) $props_txt_Arr[] = $checkin_props->getIntrigueProp()->getProp()->Name;
    if (!empty($props_txt_Arr)) $package .= "Rekvisita: ". implode(", ", $props_txt_Arr);
    
    
    $rows[] = array($group->Name, $package);
}
// add a page
$pdf->AddPage();
// print table
$pdf->Table("Grupper ska ha vid lajvstart", $header, $rows);



// close and output PDF document
$pdf->Output($name.'.pdf', 'I');
