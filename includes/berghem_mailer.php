<?php

require_once 'PHPMailer/src/Exception.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// https://mailtrap.io/blog/phpmailer/

class BerghemMailer {
    
    public static $from = 'info@berghemsvanner.se';
    
    # Normalt bör man inte anropa den här direkt utan newWithDefault
    public static function send(string $to_email, string $to_name, string $text, string $subject=null, ?array $attachments=[]) {
    
        global $current_larp;
        
        
            
        $from = static::$from;
        $myName = "Berghems vänner";
        $hej = "Hej";
            
        
        if (!is_null($current_larp)) {
            $campaign = $current_larp->getCampaign();
            if (!is_null($campaign)) {
                $from = $campaign->Email;
                $myName = $campaign->Name;
                $hej = $campaign->hej();
            }
        }
        
        if (is_null($subject)) $subject = "Meddelande från $myName";
        
        //Create a new PHPMailer instance
        $mail = new PHPMailer();
        //Set who the message is to be sent from
        $mail->setFrom($from, utf8_decode($myName));
        //Set an alternative reply-to address
        $mail->addReplyTo($from, utf8_decode($myName));
        //Set who the message is to be sent to
        $mail->addAddress($to_email, utf8_decode($to_name));
//         $mail->addAddress('mats.rappe@yahoo.se', utf8_decode($to_name));
        //Set the subject line
        $mail->Subject = utf8_decode($subject);
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        // $mail->msgHTML(file_get_contents('contents.html'), __DIR__);
        //Replace the plain text body with one created manually
        $mail->AltBody = utf8_decode($text);
        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        //send the message, check for errors
        
        if (!is_null($attachments) && !empty($attachments)) {
            foreach ($attachments as $name => $attachment) {
                if (is_null($name) || is_numeric($name)) {
                    if (is_null($current_larp)) {
                        $name = "Berghemsvänner";
                    } else {
                        $name = $current_larp->Name;
                    }
                }
                if (!str_ends_with($name,'.pdf')) {
                    $name = $name.'.pdf';
                }
                $mail->AddStringAttachment($attachment, $name, 'base64', 'application/pdf');
            }
        }

        
        $mail->isHTML(true);
        
        if (is_null($current_larp)) {
            $mailContent = "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Brev från Berghems vänner</title>
        	</head>
        	<body class='loggedin'>
                $hej $to_name!<br />
                <p>$text</p>
                
                <br />
                <p>Med vänliga hälsningar<br /><br /><b>Administratörerna</b></p>
            </body>";
        } else {        
            $mailContent = "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>$current_larp->Name</title>
        	</head>
        	<body class='loggedin'>
                $hej $to_name!<br />
                <p>$text</p>
            
                <br />
                <p>Med vänliga hälsningar<br /><br /><b>Arrangörerna av $current_larp->Name</b></p>
            </body>";
        }
        
        $mail->Body = utf8_decode($mailContent);
        
        if (!$mail->send()) {
            echo 'Mailer Error: ' . $mail->ErrorInfo;
            return false;
        } 
//         echo 'Message sent!';
        return true;
            
    } // End contruct
    
    
    public static function send_guardian_mail(Person $guardian, Person $minor, LARP $larp) {
        $text  = "$minor->Name har angett dig som ansvarig vuxen på lajvet $larp->Name<br>\n";
        $text .= "Om det inte stämmer måste du kontakta arrangörerna på ".$larp->getCampaign()->Email.
        " så att vi kan kontakta $minor->Name och reda ut det.\n";
        $text .= "<br>\n";
        
        
        static::send($guardian->Email, $guardian->Name, $text, "Ansvarig vuxen för $minor->Name på $larp->Name");
        
        
    }
    
    
    public static function send_added_role_mail(Role $role, Larp $larp) {
        $person = $role->getPerson();

        
        $campaign = $larp->getCampaign();
        
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
        $text .= "Du kan manuellt ta bort rollen ur gruppen om det är fel.";
        $text .= "<br>\n";
        
        static::send($admin_person->Email, $admin_person->Name, $text, "Anmälan till $group->Name i $larp->Name");
    }
    
    public static function send_registration_mail(Registration $registration) {
        $person = $registration->getPerson();
        
        $larp = $registration->getLARP();
        $roles = $person->getRolesAtLarp($larp);
        
        $campaign = $larp->getCampaign();
        
        $text  = "Du har nu anmält att du ska vara med i lajvet $larp->Name<br>\n";
        $text .= "För att vara helt anmäld måste du nu betala $registration->AmountToPay SEK till $campaign->Bankaccount ange referens: <b>$registration->PaymentReference</b>.<br>\n";
        if (!$person->isMember($larp)) {
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
    
    
    
    
    
}




