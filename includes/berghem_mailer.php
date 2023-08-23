<?php

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";
require_once $root . '/pdf/character_sheet_pdf.php';
require_once $root . '/pdf/group_sheet_pdf.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// https://mailtrap.io/blog/phpmailer/

class BerghemMailer {
    
    public static $from = 'info@berghemsvanner.se';
    
    # Normalt bör man inte anropa den här direkt utan newWithDefault
    # Attachments skall vara en array med namnen på filerna som nyckel.
    # to_email kan vara en sträng med en epostadress eller en array av sådana strängar
    public static function send($to_email, string $to_name, string $text, string $subject=null, ?array $attachments=[], ?string $cc="") {
    
        global $current_user;
        
        //Om test, skicka bara till inloggad användare
        if (str_contains($_SERVER['HTTP_HOST'], 'localhost')) {
            # Fixa så inga mail går iväg om man utvecklar
            if (isset($current_user)) {
                $to_email = $current_user->Email;
            } else {
                $to_email = "karin@tellen.se";
            }
           
            
        } elseif (is_array($to_email)) {
            
            # Om man skickar in en array av epostadresser skapar vi ett mail per 15 adresser.
            foreach( array_chunk($to_email,15) as $emails_to) {
                Email::normalCreate(serialize($emails_to), $to_name, $subject, $text, $attachments);
            }
            return true;
        }

        Email::normalCreate($to_email, $to_name, $subject, $text, $attachments);

        return true;
            
    }
    
    
    public static function send_guardian_mail(Person $guardian, Person $minor, LARP $larp) {
        $text  = "$minor->Name har angett dig som ansvarig vuxen på lajvet $larp->Name<br>\n";
        $text .= "Om det inte stämmer måste du kontakta arrangörerna på ".$larp->getCampaign()->Email.
        " så att vi kan kontakta $minor->Name och reda ut det.\n";
        $text .= "<br>\n";
        
        
        static::send($guardian->Email, $guardian->Name, $text, "Ansvarig vuxen för $minor->Name på $larp->Name");
    }
    
    
    public static function send_added_role_mail(Role $role, Larp $larp) {
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

        
        static::send($person->Email, $person->Name, $text, "Tilläggsanmälan till $larp->Name");
    }
    
    public static function send_registration_information_mail_to_group(Role $role, Group $group, Larp $larp) {
        $admin_person = $group->getPerson();
        
        $text  = "$role->Name är anmäld till $group->Name.<br>\n";
        $text .= "Det gäller lajvet $larp->Name.<br>\n";
        $text .= "<br>\n";
        $text .= "Du kan manuellt ta bort karaktären ur gruppen om det är fel.";
        $text .= "<br>\n";
        
        static::send($admin_person->Email, $admin_person->Name, $text, "Anmälan till $group->Name i $larp->Name");
    }
    
    public static function send_registration_mail(Registration $registration) {
        $person = $registration->getPerson();
        
        $larp = $registration->getLARP();
        $roles = $person->getRolesAtLarp($larp);
        
        $campaign = $larp->getCampaign();
        
        $text  = "Du är nu anmäld för att vara med i lajvet $larp->Name<br>\n";
        $text .= "För att vara helt anmäld måste du nu betala $registration->AmountToPay SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>. Betalas senast ".$registration->paymentDueDate()."<br>\n";
        if (!$registration->isMember()) {
            $text .= "Du måste också vara medlem i Berghems vänner. Om du inte redan är medlem kan du bli medlem <b><a href='https://ebas.sverok.se/signups/index/5915' target='_blank'>här</a></b><br>\n";
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
        
        static::send($person->Email, $person->Name, $text, "Bekräftan av anmälan till $larp->Name");
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
        
        static::send($person->Email, $person->Name, $text, "Bekräftan av reservanmälan till $larp->Name");
    }
    
    
    
    public static function send_approval_mail(Registration $registration) {
        $person = $registration->getPerson();
        $mail = $person->Email;
        
        $larp = $registration->getLARP();
        $roles = $person->getRolesAtLarp($larp);
        
        
        $text  = "Dina karaktärer är nu godkända för att vara med i lajvet $larp->Name<br>\n";
        $text .= "<br>\n";
        $text .= "De karaktärer du har anmält är:<br>\n";
        $text .= "<br>\n";
        foreach ($roles as $role) {
            $text .= '* '.$role->Name;
            if ($role->isMain($larp)) {
                $text .= " - Din huvudkaraktär";
            }
            $text .= "<br>\n";
        }
        
        $sheets = Array();
        foreach($roles as $role) {
            $pdf = new CharacterSheet_PDF();
            $pdf->SetTitle(utf8_decode('Karaktärsblad '.$role->Name));
            $pdf->SetAuthor(utf8_decode($larp->Name));
            $pdf->SetCreator('Omnes Mundos');
            $pdf->AddFont('Helvetica','');
            $pdf->SetSubject(utf8_decode($role->Name));
            $pdf->new_character_sheet($role, $larp);
            
            $sheets[scrub($role->Name)] = $pdf->Output('S');
            
            $group = $role->getGroup();
            if (!empty($group)) {
                $pdf = new Group_PDF();
                $title = 'Gruppblad '.$group->Name ;
                $pdf->SetTitle(utf8_decode($title));
                $pdf->SetAuthor(utf8_decode($larp->Name));
                $pdf->SetCreator('Omnes Mundos');
                $pdf->AddFont('Helvetica','');
                $subject = $group->Name;
                $pdf->SetSubject(utf8_decode($subject));
                $pdf->new_group_sheet($group, $larp, false);

                $sheets[scrub($group->Name)] = $pdf->Output('S');
            }
        }
        
        static::send($mail, $person->Name, $text, "Godkända karaktärer till ".$larp->Name, $sheets);
    }
    
    
    
    public static function send_spot_at_larp(Registration $registration) {
        $person = $registration->getPerson();
        $mail = $person->Email;
        
        $larp = $registration->getLARP();
        $roles = Role::getRegistredRolesForPerson($person, $larp);

        $larpStartDateText = substr($larp->StartDate, 0, 10);

        $text  = "Du är nu fullt anmäld till lajvet Så nu är det bara att vänta in lajvstart. ";
        $text .= "$larpStartDateText ses vi på $larp->Name.<br>\n";
        $text .= "<br>\n";
        $text .= "Närmare lajvet kommer intriger och information om boende.<br>\n";

        $sheets = Array();
        foreach($roles as $role) {
            $pdf = new CharacterSheet_PDF();
            $pdf->SetTitle(utf8_decode('Karaktärsblad '.$role->Name));
            $pdf->SetAuthor(utf8_decode($larp->Name));
            $pdf->SetCreator('Omnes Mundos');
            $pdf->AddFont('Helvetica','');
            $pdf->SetSubject(utf8_decode($role->Name));
            $pdf->new_character_sheet($role, $larp);

            
            $sheets[scrub($role->Name)] = $pdf->Output('S');
            
            $group = $role->getGroup();
            if (!empty($group)) {
                $pdf = new Group_PDF();
                $title = 'Gruppblad '.$group->Name ;
                $pdf->SetTitle(utf8_decode($title));
                $pdf->SetAuthor(utf8_decode($larp->Name));
                $pdf->SetCreator('Omnes Mundos');
                $pdf->AddFont('Helvetica','');
                $subject = $group->Name;
                $pdf->SetSubject(utf8_decode($subject));
                $pdf->new_group_sheet($group, $larp, false);
                $sheets[scrub($group->Name)] = $pdf->Output('S');
            }
        }
        
        static::send($mail, $person->Name, $text, "Plats på ".$larp->Name, $sheets);        
    }
    
    public static function sendNPCMail(NPC $npc) {
 
        $person = $npc->getPerson();
        $mail = $person->Email;
        
        $larp = $npc->getLARP();
        
        
        $text  = "Du har fått en NPC på lajvet $larp->Name<br>\n";
        $text .= "<br>\n";
        $text .= "Namn: $npc->Name";
        $text .= "<br>\n";
        $text .= "Beskrivning: $npc->Description";
        $text .= "<br>\n";
        $text .= "Tiden när vi vill att du spelar npc'n: $npc->Time";
        $text .= "<br>\n";
        
        
        static::send($mail, $person->Name, $text, "NPC på ".$larp->Name);
    }
    
    # Skicka mail till någon
    public static function sendContactMailToSomeone(String $email, String $name, String $subject, String $text) {
        BerghemMailer::send($email, $name, $text, $subject, BerghemMailer::findAttachment());
    }
    
    # Skicka mail till alla deltagare
    public static function sendContactMailToAll(LARP $larp, String $text) {
        $campaign = $larp->getCampaign();
        $subject = "Meddelande från $campaign->Name";
        
        # https://www.w3schools.com/php/func_array_chunk.asp
        $receiver_emails = array();
        $persons = Person::getAllRegistered($larp, false);
        foreach($persons as $person) {
            $registration = $person->getRegistration($larp);
            if (empty($registration)) continue;
            if (!$registration->hasSpotAtLarp()) continue;
            $receiver_emails[] = $person->Email;
        }
        if (empty($receiver_emails)) return;
        BerghemMailer::send($receiver_emails, '', $text, $subject, BerghemMailer::findAttachment());
    }
    
    public static function sendContactMailToAllGroupLeaders(LARP $larp, String $text) {
        $subject = "Meddelande till alla gruppledarna i $larp->Name";
        
        $persons = array();
        $receiver_emails = array();
        
        $groups = Group::getAllRegistered($larp);
        if (empty($groups)) return;
        
        foreach($groups as $group) {
            $persons[] = $group->getPerson();
        }
        if (empty($persons)) return;

        foreach($persons as $person) {
       # Än så länge struntar vi om gruppledaren verkligen har plats på lajvet. Det får dom lösa själva.
//             $registration = $person->getRegistration($larp);
//             if (empty($registration)) continue;
//             if (!$registration->hasSpotAtLarp()) continue;
            $receiver_emails[] = $person->Email;
        }
        if (empty($receiver_emails)) return;
        
        BerghemMailer::send($receiver_emails, 'Gruppledare', $text, $subject, BerghemMailer::findAttachment());
    }
    
    # Skicka mail till alla deltagare
    public static function sendContactMailToAllOfficals(LARP $larp, OfficialType $officialType, String $text) {       
        $subject = "Meddelande till alla $officialType->Name i $larp->Name";
        
        $receiver_emails = array();
        $persons = Person::getAllOfficialsByType($officialType, $larp);
        foreach($persons as $person) {
            $registration = $person->getRegistration($larp);
            if (empty($registration)) continue;
            if (!$registration->hasSpotAtLarp()) continue;
            $receiver_emails[] = $person->Email;
        }
        if (empty($receiver_emails)) return;

        BerghemMailer::send($receiver_emails, $officialType->Name, $text, $subject, BerghemMailer::findAttachment());
    }
    
    # Plocka fram standardbilagorna
    public static function findAttachment() {        
        if(!isset($_FILES['bilaga'])) return array();
        if ($_FILES["bilaga"]["size"] > 5242880) return array();
        
        $file_tmp  = $_FILES['bilaga']['tmp_name'];
        if(empty($file_tmp)) return array();
        $fileSize = filesize($file_tmp);
        if ($fileSize > 5242880) return array();
        
//         $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
//         $filetype = finfo_file($fileinfo, $file_tmp);
//         if ($file_type != "application/pdf") return array();
//         if(!str_ends_with($file_tmp, 'pdf') && !str_ends_with($file_tmp, 'PDF') && !str_ends_with($file_tmp, 'Pdf') && !str_ends_with($file_tmp, 'PdF')) return array();
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
    public static function sendIntrigues(LARP $larp, $text) {
        $subject = "Intriger för $larp->Name";
        
        $persons = Person::getAllRegistered($larp, false);
        foreach($persons as $person) {
            $registration = $person->getRegistration($larp);
            if (empty($registration)) continue;
            if (!$registration->hasSpotAtLarp()) continue;
            //TODO skapa upp alla karaktärsblad och bifoga, även gruppens karaktärsblad + pdf'er från intriger
            //BerghemMailer::send($person->Email, $person->Name, $text, $subject, null);
        }
    }
    

    # Skicka ut boendet till alla deltagare
    public static function sendHousing(LARP $larp, $text) {
        $subject = "Boende för $larp->Name";
        
        $houses = House::all();
        foreach($houses as $house) {
            $personsInHouse = Person::personsAssignedToHouse($house, $larp);
            if (empty($personsInHouse)) continue;
            $type = "hus";
           
            $preposition = "i";
            if ($house->isCamp()) {
                $type = "lägerplats";
                $preposition = "på";
            }
            $count_others = count($personsInHouse) - 1;    
            $housetext = $text . "<br><br>Du kommer att bo $preposition $type $house->Name tillsammans med $count_others andra personer.<br>".
		      "Beskrivning av $house->Name: $house->Description<br>".
		      "Vägbeskrivning: $house->PositionInVillage";
            
            $receiver_emails = array();
            
            foreach ($personsInHouse as $person) {
                $registration = $person->getRegistration($larp);
                if (empty($registration)) continue;
                if (!$registration->hasSpotAtLarp()) continue;
                if ($registration->NotComing == 1) continue;
                $receiver_emails[] = $person->Email;
            }
            if (empty($receiver_emails)) continue;
            
            $subject = "Boende för $larp->Name ($house->Name)";
            BerghemMailer::send($receiver_emails, "", $housetext, $subject, null);
        }
    }
    
    
}




