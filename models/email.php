<?php

use PHPMailer\PHPMailer\PHPMailer;

class Email extends BaseModel{
    
    public  $Id;
    public  $SenderUserId;
    public  $LarpId;
    public  $From;
    public  $To; # Skall vara endera EN epostadress som en sträng eller en serialised array av epost-strängar. https://www.w3schools.com/php/func_var_serialize.asp
    public  $ToName;
    public  $CC;
    public  $Subject;
    public  $Text;
    public  $CreatedAt;
    public  $SentAt;
    public  $ErrorMessage;

    public static $orderListBy = 'CreatedAt';
    
    public static function newFromArray($post) {
        $email = static::newWithDefault();
        $email->setValuesByArray($post);
        return $email;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['SenderUserId'])) $this->SenderUserId = $arr['SenderUserId'];
        if (isset($arr['From'])) $this->From = $arr['From'];
        if (isset($arr['To'])) $this->To = $arr['To'];
        if (isset($arr['ToName'])) $this->ToName = $arr['ToName'];
        if (isset($arr['CC'])) $this->CC = $arr['CC'];
        if (isset($arr['Subject'])) $this->Subject = $arr['Subject'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
        if (isset($arr['CreatedAt'])) $this->CreatedAt = $arr['CreatedAt'];
        if (isset($arr['SentAt'])) $this->SentAt = $arr['SentAt'];
        if (isset($arr['$ErrorMessage'])) $this->ErrorMessage = $arr['$ErrorMessage'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];  
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_user, $current_larp;
        
        $email = new self();
        
        if (!is_null($current_user)) $email->SenderUserId = $current_user->Id;
        

        $email->From = 'info@berghemsvanner.se';
        
        if (!is_null($current_larp)) {
            $email->LarpId = $current_larp->Id;
            $campaign = $current_larp->getCampaign();
            if (!is_null($campaign)) {
                $email->From = $campaign->Email;
            }
        }
        $myName = $email->myName();
        $email->Subject = "Meddelande från $myName"; 
        return $email;
    }
    
    # Normala sättet att skapa ett mail som kommer skickas vid ett senare tillfälle.
    # Attachments skall vara en array med namnen på filerna som nyckel.
    # Just nu tillåter vi bara pdf:er som bilagor.
    public static function normalCreate($To, $ToName, $Subject, $Text, $attachments) {
        $email = self::newWithDefault();
        $email->To = $To;
        $email->ToName = $ToName;
        $email->Subject = $Subject;
        $email->Text = $Text; 
        if (!is_null($attachments) && !empty($attachments)) $email->SentAt = date_format(new Datetime(),"Y-m-d H:i:s"); # Förhindra att det skickas innan bilagorna sparats färdigt.
        $email->create();
        
        if (!is_null($attachments) && !empty($attachments)) {
            foreach ($attachments as $filename => $attachment) {
                if (is_null($filename) || is_numeric($filename)) {
                    if (is_null($email->larp())) {
                        $filename = utf8_decode("Berghemsvänner");
                    } else {
                        $filename = utf8_decode($current_larp->Name);
                    }
                }
                if (!str_ends_with($filename,'.pdf')) $filename = $filename.'.pdf';
                
                Attachment::normalCreate($email, $filename, $attachment);
            }
            $email->SentAt = null;
            $email->update();
        }
    }
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_email WHERE LarpId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    # Alla icke skickade mail . Sorteras med det äldsta sist, så att man kan använda array_pop istälet för arra-shift som är långsammare.
    public static function allUnsent() {
        $sql = "SELECT * FROM regsys_email WHERE `SentAt` is null and ErrorMessage is null ORDER BY ".static::$orderListBy." DESC;";
        return static::getSeveralObjectsqQuery($sql, array());
    }
    
    # Kollar om det har gått tillräckligt lång tid sedan senaste mailet skickades, så att vi kan skicka ett nytt
    # Så här står det på Ones sida https://help.one.com/hc/en-us/articles/115005594205-What-limitations-apply-to-email-size-and-sending-
    #
    # Email clients/Webmail
    # 60 emails / 1 min (only applies if the sender domain and recipient domain are the same)
    # 25 emails / 5 min
    # 250 emails / 1 hour
    # 250 recipients / 5 min
    # 1500 recipients / 1 hour
    # Med 250 mail per timma blir det 4.17 mail per minut eller ett var 15 sekund som max.
    # Eftersom man kan ha 1500 mottagare per timma blir det optimalt med 6 mottagare per mail om man gör massmail
    public static function okToSendNow() {
        # Först hittar vi tiden för när vi senast skickade något, om någonsin
        $sql = "SELECT * FROM regsys_email WHERE `SentAt` is not null ORDER BY `SentAt` DESC LIMIT 1;";
        $lastestSentEmail = static::getOneObjectQuery($sql, array());
        if (is_null($lastestSentEmail)) return true;
        
        $now = new Datetime();
        $sent_at_date_time = new DateTime($lastestSentEmail->SentAt);
        $diff = $now->getTimestamp() - $sent_at_date_time->getTimestamp();
//         echo "<h1>$diff</h1>\n";
        if ($diff > 14) return true;
        return false;
    }
    
    public function larp() {
        return LARP::loadById($this->LarpId);
    }

    # Avsändarens namn
    public function myName() { 
        $larp = $this->larp();
        if (is_null($larp)) return "Berghems vänner";
        $campaign = $larp->getCampaign();
        if (is_null($campaign)) return "Berghems vänner";
        return $campaign->Name;
    }
    
    public function receiverName() {
        if (empty($this->ToName)) return "Stranger";
        return str_replace( array( '\'', '"', ',' , ';', '<', '>' ), ' ', $this->ToName);
//         return $this->ToName;
    }
    
    public function attachments() {
        return Attachment::allByEmail($this);
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_email SET SentAt=?, SenderUserId=?, LarpId=?, `From`=?, `To`=?, `ToName`=?, `CC`=?, Subject=?, Text=?, ErrorMessage=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->SentAt, $this->SenderUserId, $this->LarpId, $this->From, $this->To, $this->ToName, $this->CC, $this->Subject, 
                                  $this->Text, $this->ErrorMessage, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_email (SenderUserId, LarpId, `From`, `To`, ToName, CC, Subject, Text, CreatedAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->SenderUserId, $this->LarpId, $this->From, $this->To, $this->ToName, $this->CC, $this->Subject, $this->Text, date_format(new Datetime(),"Y-m-d H:i:s")))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Skapar ett standardmässigt HTML-meddelande. 
    # Så att alla taggar blir rätt och vi kan skicka ett alt-meddelande utan html-skräp så blilnda lättare kan läsa mailet.
    public function mailContent() {
        $larp = $this->larp();
        
        $hej = "Hej";

        if (!is_null($larp)) {
            $campaign = $larp->getCampaign();
            if (!is_null($campaign))  $hej = $campaign->hej();
        }
        
        $to_name = $this->receiverName();
        
        if (is_null($larp)) {
            return "<!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <title>Brev från Berghems vänner</title>
        	</head>
        	<body class='loggedin'>
                $hej $to_name!<br />
                <p>$this->Text</p>
                
                <br />
                <p>Med vänliga hälsningar<br /><br /><b>Administratörerna</b></p>
            </body>";
        } 
        
        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>$larp->Name</title>
    	</head>
    	<body class='loggedin'>
            $hej $to_name!<br />
            <p>$this->Text</p>
            
            <br />
            <p>Med vänliga hälsningar<br /><br /><b>Arrangörerna av $larp->Name</b></p>
        </body>";
    }
    
    # Normalt bör man inte anropa den här direkt utan newWithDefault
    public function sendNow() {        
        global $current_larp, $current_user;
     
        
        //Create a new PHPMailer instance
        $mail = new PHPMailer();
        //Set who the message is to be sent from
        $mail->setFrom($this->From, utf8_decode($this->myName()),0);
//         $mail->setFrom($from, utf8_decode($myName)); # Tror faktiskt det ska vara så här
        //Set an alternative reply-to address
        $mail->addReplyTo($this->From, utf8_decode($this->myName()));
        //Set who the message is to be sent to
        
        if (!($to_array = @unserialize($this->To))) {
            $mail->addAddress($this->To, utf8_decode($this->receiverName()));
        } elseif (!empty($to_array)) {
            foreach($to_array as $to) {
//                 echo "<h1>TO = $to</h1>";
                $mail->addAddress($to, utf8_decode($this->receiverName()));
            }
        }

        if (!empty($this->CC)) {
            $mail->addCC($this->CC);
        }

        $mail->Subject = utf8_decode($this->Subject);
        $mail->AltBody = utf8_decode($this->Text);
        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        
        $attachments = $this->attachments();
        
        if (!is_null($attachments) && !empty($attachments)) {
            foreach ($attachments as $attachment) {
                $mail->AddStringAttachment($attachment->Attachement, $attachment->Filename, 'base64', 'application/pdf');
            }
        }
        
        
        $mail->isHTML(true);

        $mail->Body = utf8_decode($this->mailContent());

        
        
        if (!$mail->send()) {
//             echo 'Mailer Error: ' . $mail->ErrorInfo;
            $this->ErrorMessage = $mail->ErrorInfo;
            $this->update();
            return false;
        }
        
        $this->SentAt = date_format(new Datetime(),"Y-m-d H:i:s");
        $this->update();
        
        return true;
        
    }
    
    public static function handleEmailQueue() {
        //     return;
        $current_queue = static::allUnsent();
        if (empty($current_queue)) return;
        if (!static::okToSendNow()) return;
        //     echo "Send an email";
        $to_send = array_pop($current_queue); # Hämta äldsta mailet i kön
        return $to_send->sendNow();
    }
}
