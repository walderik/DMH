<?php

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/pdf/character_sheet_pdf.php';
require_once $root . '/pdf/group_sheet_pdf.php';
require_once $root . '/pdf/invoice_pdf.php';
require_once $root . '/pdf/alchemy_supplier_sheet_pdf.php';
require_once $root . '/pdf/alchemy_alchemist_sheet_pdf.php';
require_once $root . '/pdf/magic_magician_sheet_pdf.php';
require_once $root . '/pdf/house_info.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// https://mailtrap.io/blog/phpmailer/

class BerghemMailer {
    public const DaysAutomatic = 7;
    public const DaysManual = 180;
    
    const LARP = 0;
    const CAMPAIGN = 1;
    const ASSOCIATION = 2;    
    
    public static $from = 'info@berghemsvanner.se';
    
    # Normalt bör man inte anropa den här direkt utan newWithDefault
    # Attachments skall vara en array med namnen på filerna som nyckel.
    # to_email kan vara en sträng med en epostadress eller en array av sådana strängar
    public static function send($larp, $senderId, $toPersonIds, string $greeting, string $text, string $subject, string $senderText, int $noOfDaysKept, ?array $attachments=[], ?string $cc="") {
        if (is_array($toPersonIds)) {
            foreach ($toPersonIds as $toPersonId) {
                Email::normalCreate($toPersonId, $greeting, $subject, $text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);
            }
            return true;
        }

        Email::normalCreate($toPersonIds, $greeting, $subject, $text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);

        return true;   
    }
 
    public static function sendSimpleEmail($larp, $senderId, $toEmails, $name, string $greeting, string $text, string $subject, string $senderText, int $noOfDaysKept, ?array $attachments=[], ?string $cc="") {
        if (is_array($toEmails)) {
            foreach ($toEmails as $toEmail) {
                Email::normalCreateSimple($toEmail, $name, $greeting, $subject, $text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);
            }
            return true;
        }
        Email::normalCreateSimple($toEmails, $name, $greeting, $subject, $text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);
        
        return true;
    }
    
    public static function send_guardian_mail(Person $guardian, Person $minor, LARP $larp) {
        $text  = "$minor->Name har angett dig som ansvarig vuxen på lajvet $larp->Name<br>\n";
        $text .= "Om det inte stämmer måste du kontakta arrangörerna på ".$larp->getCampaign()->Email.
        " så att vi kan kontakta $minor->Name och reda ut det.\n";
        $text .= "<br>\n";
        
        
        BerghemMailer::send($larp, NULL, $guardian->Id, "Hej ".$guardian->Name, $text, "Ansvarig vuxen för $minor->Name på $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_user_changed(Person $person, User $new_user) {
        $text  = "Du har nu $person->Name på ditt konto i Omnes Mundi.\n";
        $text .= "<br>\n";
        
        BerghemMailer::sendSimpleEmail(NULL, NULL, array($new_user->Email), $new_user->Name, 'Hej', $text, "Byte av konto för $person->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    
    public static function send_added_role_mail(Role $role, Larp $larp, $senderId) {
        $person = $role->getPerson();
        
        $text  = "Arrangörerna har lagt till en karaktär till din anmälan till lajvet $larp->Name<br>\n";
        $text .= "<br>\n";

        $text .= '* '.$role->Name;
        if (isset($role->GroupId)) {
            $group = $role->getGroup();
            $text .= ", medlem i $group->Name";
            static::send_registration_information_mail_to_group($role, $group, $larp);
        }
        
        $text .= "<br>\n";

        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Tilläggsanmälan till $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_registration_information_mail_to_group(Role $role, Group $group, Larp $larp) {
        $admin_person = $group->getPerson();
        $player = $role->getPerson();
        $text  = "$role->Name ";
        if ($player->hasPermissionShowName()) {
            $text .= "spelad av $player->Name ";
        }
        $text .= "är anmäld till $group->Name.<br>\n";
        $text .= "Det gäller lajvet $larp->Name.<br>\n";
        $text .= "<br>\n";
        $text .= "Du kan manuellt ta bort karaktären ur gruppen om det är fel.";
        $text .= "<br>\n";
        
        BerghemMailer::send($larp, null, $admin_person->Id, "Hej ".$admin_person->Name, $text, "Anmälan till $group->Name i $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_registration_mail(Registration $registration) {
        $person = $registration->getPerson();
        
        $larp = $registration->getLARP();
        $roles = $person->getRolesAtLarp($larp);
        
        $campaign = $larp->getCampaign();
        
        $text  = "Du är nu anmäld för att vara med i lajvet $larp->Name<br>\n";
        
        if ($registration->AmountToPay == 0) {
            $text .= "Din avgift för lajvet är 0 SEK.<br>";
            
        } else {
            $text .= "För att vara helt anmäld måste du nu betala $registration->AmountToPay SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>. ";
            $text .= "Betalas senast ".$registration->paymentDueDate()."<br>\n";
            $host = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
            if (!empty($campaign->SwishNumber)) $text .= "<br><img width='200' src='$host/regsys/includes/display_image.php?Swish=1&RegistrationId=$registration->Id&CampaignId=$campaign->Id'/><br>\n";
        }
        
            $text .= "Din anmälan som deltagare innebär inte att din karaktär är godkänd, utan det är en separat process.</b><br>\n";
        
        if (!$registration->isMember()) {
            $currentYear = date("Y");
            $larpYear = substr($larp->StartDate, 0, 4);
            if ($currentYear == $larpYear) {
                $text .= "Du måste också vara medlem i Berghems vänner. Om du inte redan är medlem kan du bli medlem <b><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>här</a></b><br>\n";
            } else {
                $text .= "Du måste också vara medlem i Berghems vänner för år $larpYear. Det kan du först bli efter nyår, så vänta tills dess med att betala in avgiften.</b><br>\n";
            }
        }
        $text .= "<br>\n";
        $text .= "Vi kommer att gå igenom karaktärerna du har anmält och godkänna dom för spel.<br>\n";
        $text .= "<br>\n";
        $text .= "De karaktärer du har anmält är:<br>\n";
        $text .= "<br>\n";
        foreach ($roles as $role) {
            $text .= '* '.$role->Name;
            if ($role->isMain($larp)) {
                $text .= " - Din huvudkaraktär";
            } 
            if (isset($role->GroupId)) {
                $group = $role->getGroup();
                $text .= ", medlem i $group->Name";
                static::send_registration_information_mail_to_group($role, $group, $larp);
            }
            
            $text .= "<br>\n";
        }
        
        BerghemMailer::send($larp, null, $person->Id, "Hej ".$person->Name, $text, "Bekräftan av anmälan till $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_groupregistration_mail(LARP_Group $larp_group, Person $sender) {
        $group = $larp_group->getGroup();
        $group_leader = $group->getPerson();
        $larp = $larp_group->getLARP();
        $campaign = $larp->getCampaign();
        
        $text  = "Gruppen $group->Name är nu anmäld för att vara med i lajvet $larp->Name<br>\n";
        if ($group_leader->Id != $sender->Id) {
            $text  .= "Gruppen har anmälts av $sender->Name.<br>\n";
            
        }
        $text .= "<br>\n";
        if (!$group->isApproved()) {
            $text .= "Vi kommer nu att kontrollera att gruppen är godkänd. När den är godkänd kan gruppens medlemmar anmäla sig.\n";
        } else {
            $text .= "Nu kan gruppens medlemmar anmäla sig.\n";
        }
         
        BerghemMailer::send($larp, null, $group_leader->Id, "Hej ".$group_leader->Name, $text, "Bekräftan av gruppanmälan till $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_updatedpayment_mail(Registration $registration, $senderId) {
        $person = $registration->getPerson();
        
        $larp = $registration->getLARP();
        $campaign = $larp->getCampaign();
        
        $text  = "Din avgift för lajvet lajvet $larp->Name har ändrats.<br>\n";
        if ($registration->AmountToPay == 0) {
            $text .= "Din avgift för lajvet är 0 SEK.<br>";
            
        } else {
            $text .= "Du ska nu betala $registration->AmountToPay SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>. ";
            $text .= "Betalas senast ".$registration->paymentDueDate()."<br>\n";
            $host = (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
            if (!empty($campaign->SwishNumber)) $text .= "<br><img width='200' src='$host/regsys/includes/display_image.php?Swish=1&RegistrationId=$registration->Id&CampaignId=$campaign->Id'/><br>\n";
        }
        if (!$registration->isMember()) {
            $currentYear = date("Y");
            $larpYear = substr($larp->StartDate, 0, 4);
            if ($currentYear == $larpYear) {
                $text .= "Du måste också vara medlem i Berghems vänner. Om du inte redan är medlem kan du bli medlem <b><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>här</a></b><br>\n";
            } else {
                $text .= "Du måste också vara medlem i Berghems vänner $larpYear.</b><br>\n";
            }
        }
        $text .= "<br>\n";
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Uppdaterad avgift till $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_reserve_registration_mail(Reserve_Registration $reserve_registration) {
        $person = $reserve_registration->getPerson();
        
        $larp = $reserve_registration->getLARP();
        $roles = $person->getReserveRolesAtLarp($larp);
        
        $text  = "Lajvet $larp->Name är fullt, men du står nu på reservlistan.<br>".
                 "Betala inte in något nu. Om du får en plats på lajvet kommer du att få ett mail med information om hur mycket och vart du ska betala.<br><br>\n";
        $text .= "De karaktärer du har anmält är:<br>\n";
        $text .= "<br>\n";
        foreach ($roles as $role) {
            $text .= '* '.$role->Name;
            if ($role->isMain($larp)) {
                $text .= " - Din huvudkaraktär";
            }
            if (isset($role->GroupId)) {
                $group = $role->getGroup();
                $text .= ", medlem i $group->Name";
            }
            
            $text .= "<br>\n";
        }
        
        BerghemMailer::send($larp, null, $person->Id, "Hej ".$person->Name, $text, "Bekräftan av reservanmälan till $larp->Name", "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_remove_reserve_registration_mail(Reserve_Registration $reserve_registration, $sender_id) {
        $person = $reserve_registration->getPerson();
        $larp = $reserve_registration->getLARP();

        $text  = "Du är inte längre registrerad på reservlistan för lajvet $larp->Name.<br><br>Kontakta arrangörerna på ".$larp->getCampaign()->Email." ifall du har frågor och funderingar.\n";
        
        BerghemMailer::send($larp, $sender_id, $person->Id, "Hej, ".$person->Name, $text, "Borttagen från reservlistan för ".$larp->Name, "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_role_approval_mail(Role $role, LARP $larp, $senderId) {
        $person = $role->getPerson();
        $mail = $person->Email;
        
        $text  = "Din karaktär $role->Name är nu godkänd för att vara med i lajvet $larp->Name<br>\n";
        
        $sheets = static::getAllSheets(array($role), $larp);
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Godkänd karaktär till ".$larp->Name, "", BerghemMailer::DaysAutomatic, $sheets);
    }
    
    public static function send_role_unapproval_mail(Role $role, LARP $larp, $senderId) {
        $person = $role->getPerson();
        $text  = "Din karaktär $role->Name är inte längre godkänd för att vara med i lajvet $larp->Name.<br>Kontakta arrangörerna på ".$larp->getCampaign()->Email."för att prata med dem om vad du behöver göra för att få din karaktär godkänd.\n";
        
        $sheets = static::getAllSheets(array($role), $larp);
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Icke godkänd karaktär till ".$larp->Name, "", BerghemMailer::DaysAutomatic, $sheets);
    }
    
    public static function send_group_approval_mail(Group $group, LARP $larp, $senderId) {
        $person = $group->getPerson();
        
        $text  = "Din grupp $group->Name är nu godkänd för att vara med i lajvet $larp->Name.\n";
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Godkänd grupp till ".$larp->Name, "", BerghemMailer::DaysAutomatic);
    } 
    
    public static function send_group_unapproval_mail(Group $group, LARP $larp, $senderId) {
        $person = $group->getPerson();
        
        $text  = "Din grupp $group->Name är inte längre godkänd för att vara med i lajvet $larp->Name.<br>Kontakta arrangörerna på ".$larp->getCampaign()->Email."för att prata med dem om vad du behöver göra för att få din grupp godkänd.\n";
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Godkänd grupp till ".$larp->Name, "", BerghemMailer::DaysAutomatic);
    }
    
    public static function send_spot_at_larp(Registration $registration, $senderId) {
        $person = $registration->getPerson();
        
        $larp = $registration->getLARP();
        $roles = Role::getRegistredRolesForPerson($person, $larp);

        $larpStartDateText = substr($larp->StartDate, 0, 10);

        $text  = "Du är nu fullt anmäld till lajvet Så nu är det bara att vänta in lajvstart. ";
        $text .= "$larpStartDateText ses vi på $larp->Name.<br>\n";
        $text .= "<br>\n";
        $text .= "Närmare lajvet kommer intriger och information om boende.<br>\n";

        $sheets = static::getAllSheets($roles, $larp); 
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "Plats på ".$larp->Name, "", BerghemMailer::DaysAutomatic, $sheets);        
    }
    
    
    public static function sendNPCMail(NPC $npc, $senderId) {
 
        $person = $npc->getPerson();
        
        $larp = $npc->getLARP();
        
        
        $text  = "Du har fått en NPC på lajvet $larp->Name<br>\n";
        $text .= "<br>\n";
        $text .= "Namn: $npc->Name";
        $text .= "<br>\n";
        $text .= "Beskrivning: $npc->Description";
        $text .= "<br>\n";
        $text .= "Tiden när vi vill att du spelar npc'n: $npc->Time";
        $text .= "<br>\n";
        
        
        BerghemMailer::send($larp, $senderId, $person->Id, "Hej ".$person->Name, $text, "NPC på ".$larp->Name, "", BerghemMailer::DaysAutomatic);
    }
    
    # Skicka mail till någon
    public static function sendContactMailToSomeone($personId, String $greeting, String $subject, String $text, $senderText, $larp, $senderId) {
        BerghemMailer::send($larp, $senderId, $personId, $greeting, $text, $subject, $senderText, BerghemMailer::DaysManual, BerghemMailer::findAttachment());
    }
    
    public static function sendContactMailToSomeoneUnknown($email, String $greeting, String $subject, String $text, $senderText, $larp) {
        BerghemMailer::sendSimpleMail($larp, $email, $greeting, $text, $subject, $senderText, BerghemMailer::DaysManual, BerghemMailer::findAttachment());
    }
    
    # Skicka mail till alla deltagare
    public static function sendContactMailToAll(String $greeting, String $subject, String $text, $senderText, $larp, $senderId) {
        $campaign = $larp->getCampaign();
        if (empty($subject)) $subject = "Meddelande från $campaign->Name";
        
        # https://www.w3schools.com/php/func_array_chunk.asp
        $receivers = array();
        $persons = Person::getAllRegistered($larp, false);
        foreach($persons as $person) {
            $registration = $person->getRegistration($larp);
            if (empty($registration)) continue;
            if ($registration->isNotComing()) continue;
            $receivers[] = $person->Id;
        }
        if (empty($receivers)) return;
        BerghemMailer::send($larp, $senderId, $receivers, $greeting, $text, $subject, $senderText, BerghemMailer::DaysManual, BerghemMailer::findAttachment());
    }

    
    public static function sendContactMailToSeveral($personIdArr, String $greeting, String $subject, String $text, $senderText, $larp, $senderId) {
        if (empty($personIdArr)) return;
        
        BerghemMailer::send($larp, $senderId, $personIdArr, $greeting, $text, $subject, $senderText, BerghemMailer::DaysManual, BerghemMailer::findAttachment());
    }
    
     # Plocka fram standardbilagorna
    public static function findAttachment() {        
        if(!isset($_FILES['bilaga'])) return array();
        if ($_FILES["bilaga"]["size"] > 5242880) return array();
        
        $file_tmp  = $_FILES['bilaga']['tmp_name'];
        if(empty($file_tmp)) return array();
        $fileSize = filesize($file_tmp);
        if ($fileSize > 5242880) return array();

        $allowed = array("pdf" => "application/pdf");
        $filetype = $_FILES["bilaga"]["type"];
        $file_name = $_FILES['bilaga']['name'];
        
        // Validate file extension
        $ext = pathinfo($file_name, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) return array();
        // Validate type of the file
        if(!in_array($filetype, $allowed)) return array();
        
        
        
        $the_file = file_get_contents($file_tmp);
        
        $attachments = array();
        $attachments[$file_name] = $the_file;
        return $attachments;
    }
    
    
    # Skicka ut intrigerna/karaktärsbladen till alla deltagare
    public static function sendIntrigues($greeting, $subject, $text, $senderText, LARP $larp, $senderId) {
        $subject = "Intriger för $larp->Name";
        
        $persons = Person::getAllRegistered($larp, false);
        foreach($persons as $person) {
            $registration = $person->getRegistration($larp);
            if (empty($registration)) continue;
            if (!$registration->hasSpotAtLarp()) continue;
            $roles = Role::getRegistredRolesForPerson($person, $larp); 
            $rolesText = "";
            
            if (!empty($roles)) {
                $rolesText .= "De karaktärer du ska spela är:<br>\n";
                $rolesText .= "<br>\n";
                foreach ($roles as $role) {
                    $rolesText .= '* '.$role->Name;
                    if ($role->isMain($larp)) {
                        $rolesText .= " - Din huvudkaraktär";
                    }
                    $rolesText .= "<br>\n";
                }
                
            }
            
            $sheets = static::getAllSheets($roles, $larp);
            
            $npcs = NPC::getReleasedNPCsForPerson($person, $larp);
            $npcText = "";
            
            if (!empty($npcs)) {
                $npcText  = "<br>De NPC'er du ska spela är:<br>\n";
                $npcText .= "<br>\n";
                foreach($npcs as $npc) {
                    $npcText .= "Namn: $npc->Name";
                    $npcText .= "<br>\n";
                    $npcText .= "Beskrivning: $npc->Description";
                    $npcText .= "<br>\n";
                    $npcText .= "Tiden när vi vill att du spelar npc'n: $npc->Time";
                    $npcText .= "<br>\n";
                
                }
            }
            
            $printText = "<br>Skriv ut de bifogade filerna och ta med till lajvet.<br>";
            
            $sendtext = $text . "<br><br>". $rolesText . $npcText . $printText;

            BerghemMailer::send($larp, $senderId, $person->Id, $greeting, $sendtext, $subject, $senderText, BerghemMailer::DaysAutomatic, $sheets);
        }
    }
    
    
    public static function sendInvoice(Invoice $invoice, $senderId) {
        if ($invoice->isSent()) return;
        $larp = $invoice->getLarp();
        $person = $invoice->getContactPerson();
        $subject = "Faktura från $larp->Name";
        
        $sendtext = "Översänder faktura enligt överenskommelse.<br>\n";
        
        $pdf = new Invoice_PDF();
        $pdf->SetTitle('Faktura');
        $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
        $pdf->SetCreator('Omnes Mundi');
        $pdf->AddFont('SpecialElite','');
        $pdf->SetSubject('Faktura');
        $pdf->ny_faktura($invoice);
        
        $sheets = array();
        $sheets["Faktura"] = $pdf->Output('S'); 
        
        $invoice->setSent();

        BerghemMailer::send($larp, $senderId, array($person->Id), "Hej ".$person->Name, $sendtext, $subject, "", BerghemMailer::DaysAutomatic, $sheets);
    }
    

    # Skicka ut boendet till alla deltagare
    public static function sendHousing($greeting, $subject, $text, $senderText, LARP $larp, $senderId) {

        
        $houses = House::all();
        foreach($houses as $house) {
            $subject_house = "";
            
            $personsInHouse = Person::personsAssignedToHouse($house, $larp);
            if (empty($personsInHouse)) continue;
            $type = "hus";
           
            $preposition = "i";
            if ($house->isCamp()) {
                $type = "lägerplats";
                $preposition = "på";
            }
            $count_others = count($personsInHouse) - 1;    
            $housetext = $text . "<br><br>Du kommer att bo $preposition $type $house->Name tillsammans med $count_others andra personer.<br><br>".
		      "Beskrivning av $house->Name: $house->Description<br><br>".
		      "Vägbeskrivning: $house->PositionInVillage<br><br>".
		      "Om du vill veta mer om ditt hus kan du titta på <a href='http://main.berghemsvanner.se/husen-i-byn/'>http://main.berghemsvanner.se/husen-i-byn/</a> ".
		      "eller logga in i Omnes Mundi <a href='https://www.berghemsvanner.se/regsys/'>https://www.berghemsvanner.se/regsys/</a>.";

            
            $receivers = array();
            $names_in_house = array();
            foreach ($personsInHouse as $person) {
                $registration = $person->getRegistration($larp);
                if (empty($registration)) continue;
                if (!$registration->hasSpotAtLarp()) continue;
                if ($registration->NotComing == 1) continue;
                $receivers[] = $person->Id;
                if ($person->hasPermissionShowName()) $names_in_house[] = $person->Name;
                else $names_in_house[] = "(Vill inte visa sitt namn)";
            }
            if (empty($receivers)) continue;
            
            $housetext = $housetext . "<br><br>De som bor i huset är: ".implode(", ", $names_in_house);
            
            
            $subject_house = $subject . " ($house->Name)";
            
            $sheets = array();
            if (!empty(trim($house->NotesToUsers))) {
                $name = 'Husbrev';
                
                $pdf = new HouseInfo();
                $name = "Husbrev för $house->Name";
                $pdf->init('Omnes Mundi', $name, $larp->Name, false);
                $pdf->AddPage();
                $pdf->printInfo($name, $house->NotesToUsers);
                $sheets[scrub($house->Name)] = $pdf->Output($house->Name.'.pdf','S');
            }
            
            BerghemMailer::send($larp, $senderId, $receivers, $greeting, $housetext, $subject_house, $senderText, BerghemMailer::DaysAutomatic, $sheets);
        }
    }
    
    public static function getAllSheets($roles, LARP $larp) {
        $sheets = Array();
        foreach($roles as $role) {
            $pdf = new CharacterSheet_PDF();
            $pdf->SetTitle(encode_utf_to_iso('Karaktärsblad '.$role->Name));
            $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
            $pdf->SetCreator('Omnes Mundi');
            $pdf->AddFont('Helvetica','');
            $pdf->SetSubject(encode_utf_to_iso($role->Name));
            $pdf->new_character_sheet($role, $larp);
            
            $sheets[scrub($role->Name)] = $pdf->Output('S');
            
            //Om man är magiker ska det också skickas med
            $magician = Magic_Magician::getForRole($role);
            if (!empty($magician)) {
                $pdf = new MagicMagicianSheet_PDF();
                $title = 'Magiker '.$role->Name;
                
                $pdf->SetTitle(encode_utf_to_iso($title));
                $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
                $pdf->SetCreator('Omnes Mundi');
                $pdf->AddFont('Helvetica','');
                $subject = $title;
                $pdf->SetSubject(encode_utf_to_iso($subject));

                $pdf->single_magician_sheet($magician, $larp);
                
                $sheets[scrub($title)] = $pdf->Output('S');
            }

            
            //Om man är alkemist ska det också skickas med
            $alchemist = Alchemy_Alchemist::getForRole($role);
            if (!empty($alchemist)) {
                $pdf = new AlchemyAlchemistSheet_PDF();
                $title = 'Alkemist '.$role->Name;
                
                $pdf->SetTitle(encode_utf_to_iso($title));
                $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
                $pdf->SetCreator('Omnes Mundi');
                $pdf->AddFont('Helvetica','');
                $subject = $title;
                $pdf->SetSubject(encode_utf_to_iso($subject));
                

                $pdf->single_alchemist_sheet($alchemist, $larp);
                
                $sheets[scrub($title)] = $pdf->Output('S');
            }
 
            //Om man är lövjerist ska det också skickas med
            $supplier = Alchemy_Supplier::getForRole($role);
            if (!empty($supplier)) {
                $pdf = new AlchemySupplierSheet_PDF();
                $title = 'Lövjerist '.$role->Name;
                
                $pdf->SetTitle(encode_utf_to_iso($title));
                $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
                $pdf->SetCreator('Omnes Mundi');
                $pdf->AddFont('Helvetica','');
                $subject = $title;
                $pdf->SetSubject(encode_utf_to_iso($subject));
                

                $pdf->single_supplier_sheet($supplier, $larp);
                
                $sheets[scrub($title)] = $pdf->Output('S');
            }
            
            //Om man ska ha syner ska det också skickas med
            if (Vision::hasVisions($larp, $role)) {
                $pdf = new VisionSheet_PDF();
                $title = 'Syner för '. $role->Name ;
                
                $pdf->SetTitle(encode_utf_to_iso($title));
                $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
                $pdf->SetCreator('Omnes Mundi');
                $pdf->AddFont('Helvetica','');
                $subject = $title;
                $pdf->SetSubject(encode_utf_to_iso($subject));
                

                $pdf->single_vision_reciever_sheet($role, $larp);
                
                $sheets[scrub($title)] = $pdf->Output('S');
            }
            
            
            if ($larp->isIntriguesReleased()) {
                $intrigues = Intrigue::getAllIntriguesForRole($role->Id, $larp->Id);
                foreach ($intrigues as $intrigue) {
                    $intrgueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $role);
                    $intrigue_Pdfs = $intrgueActor->getAllPdfsThatAreKnown();
                    foreach($intrigue_Pdfs as $intrigue_Pdf) {
                        $sheets[$intrigue_Pdf->Filename] = $intrigue_Pdf->FileData;
                    }
                }
            }
            
            $group = $role->getGroup();
            if (!empty($group)) {
                $pdf = new Group_PDF();
                $title = 'Gruppblad '.$group->Name ;
                $pdf->SetTitle(encode_utf_to_iso($title));
                $pdf->SetAuthor(encode_utf_to_iso($larp->Name));
                $pdf->SetCreator('Omnes Mundi');
                $pdf->AddFont('Helvetica','');
                $subject = $group->Name;
                $pdf->SetSubject(encode_utf_to_iso($subject));
                $pdf->new_group_sheet($group, $larp, false);
                $sheets[scrub($group->Name)] = $pdf->Output('S');
            }
        }
        return $sheets;
        
    }
}




