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
        $myName = "Beghems vänner";
        $hej = "Hej";
            
        
        if (!is_null($current_larp)) {
            $campaign = $current_larp->getCampaign();
            if (!is_null($campaign)) {
                $from = $campaign->Email;
                $myName = $campaign->Name;
                if ($campaign->Abbreviation=='DMH') $hej = "Howdy";
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
                Howdy $to_name!<br />
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
    
}