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
//         if (str_contains($_SERVER['HTTP_HOST'], 'localhost')) {
//             $to_email = $current_user->Email;
//         }
//         print_r($to_email);
        
        if (is_array($to_email)) {
            $to_email = serialize($to_email);
        }

        
        $email = Email::normalCreate($to_email, $to_name, $subject, $text, $attachments);

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
                 "Betala in in något nu. Om du får en plats på lajvet kommer du att få ett mail med information om hur mycket och vart du ska betala.<br><br>\n";
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
        
        static::send($person->Email, $person->Name, $text, "Bekräftan av anmälan till $larp->Name");
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
            $sheets[utf8_decode($role->Name)] = $pdf->Output('S');
            
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
                $pdf->new_group_sheet($group, $larp);
                $sheets[utf8_decode($group->Name)] = $pdf->Output('S');
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
            $sheets[utf8_decode($role->Name)] = $pdf->Output('S');
            
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
                $pdf->new_group_sheet($group, $larp);
                $sheets[utf8_decode($group->Name)] = $pdf->Output('S');
            }
        }
        
        static::send($mail, $person->Name, $text, "Plats på ".$larp->Name, $sheets);        
    }
    
    public static function sendNPCMail(NPC $npc) {
 
        $person = $npc->getPerson();
        $mail = $person->Email;
        
        $larp = $npc->getLARP();
        
        
        //TODO bättre text när npc är klar
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
        BerghemMailer::send($email, $name, $text, $subject);
    }
    
    # Skicka mail till alla deltagare
    public static function sendContactMailToAll(LARP $larp, String $text, $onlyHasSpotAtLarp=true) {
        $name = "";
        $campaign = $larp->getCampaign();
        $subject = "Meddelande från $campaign->Name";
        
        # https://www.w3schools.com/php/func_array_chunk.asp
        $receiver_email = array();
        $persons = Person::getAllRegistered($larp, false);
        foreach($persons as $person) {
            $registration = $person->getRegistration($larp);
            if (empty($registration)) continue;
            if ($onlyHasSpotAtLarp && !$registration->hasSpotAtLarp()) continue;
            $receiver_email[] = $person->Email;
        }
        if (empty($receiver_email)) return;
        foreach( array_chunk($receiver_email,15) as $emails_to) {
            BerghemMailer::send($emails_to, $name, $text, $subject);
        }
    }
    
}




