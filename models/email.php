<?php

use PHPMailer\PHPMailer\PHPMailer;

class Email extends BaseModel{
    
    public  $Id;
    public  $SenderPersonId;
    public  $LarpId;
    public  $From;
    public  $To; # Skall vara endera EN epostadress som en sträng eller en serialised array av epost-strängar. https://www.w3schools.com/php/func_var_serialize.asp
    public  $ToName;
    public  $CC;
    public  $Subject;
    public  $Greeting;
    public  $Text;
    public  $SenderText;
    public  $CreatedAt;
    public  $SentAt;
    public  $DeletesAt;
    public  $ErrorMessage;

    public static $orderListBy = 'CreatedAt';
    
    public static function newFromArray($post) {
        $email = static::newWithDefault();
        $email->setValuesByArray($post);
        return $email;
    }
    
    public function setValuesByArray($arr) {
        if (array_key_exists('LarpId', $arr)) $this->LarpId = $arr['LarpId'];
        if (array_key_exists('SenderPersonId', $arr)) $this->SenderPersonId = $arr['SenderPersonId'];
        if (isset($arr['From'])) $this->From = $arr['From'];
        if (isset($arr['To'])) $this->To = $arr['To'];
        if (isset($arr['ToName'])) $this->ToName = $arr['ToName'];
        if (isset($arr['CC'])) $this->CC = $arr['CC'];
        if (isset($arr['Subject'])) $this->Subject = $arr['Subject'];
        if (isset($arr['Greeting'])) $this->Greeting = $arr['Greeting'];
        if (isset($arr['Text'])) $this->Text = $arr['Text'];
        if (isset($arr['SenderText'])) $this->SenderText = $arr['SenderText'];
        if (isset($arr['CreatedAt'])) $this->CreatedAt = $arr['CreatedAt'];
        if (isset($arr['SentAt'])) $this->SentAt = $arr['SentAt'];
        if (isset($arr['DeletesAt'])) $this->DeletesAt = $arr['DeletesAt'];
        if (isset($arr['ErrorMessage'])) $this->ErrorMessage = $arr['ErrorMessage'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];  
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $email = new self();
        $email->From = 'info@berghemsvanner.se';
        return $email;
    }
    
    # Normala sättet att skapa ett mail som kommer skickas vid ett senare tillfälle.
    # Attachments skall vara en array med namnen på filerna som nyckel.
    # Just nu tillåter vi bara pdf:er som bilagor.
    public static function normalCreate($ToPersonId, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId) {
        

        $person = Person::loadById($ToPersonId);
        $ToEmail = $person->Email;
        $name = $person->Name;
        
        if ($person->isSubscribed()) $email = Email::createMail($ToEmail, $name, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);
        else $email = Email::createMessage($name, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);
        
        $email->connectToPerson($ToPersonId);
     }

     public static function normalCreateSimple($ToEmail, $name, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId) {
         Email::createMail($ToEmail, $name, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId);
      }
     
      private static function createMail($ToEmail, $name, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId) {
          $email = self::newWithDefault();
          
          $email->To = $ToEmail;
          $email->ToName = $name;
          
          $email->Greeting = $greeting;
          $email->Subject = $Subject;
          $email->Text = $Text;
          $email->SenderText = $senderText;
          $email->SenderPersonId = $senderId;
          $now = new Datetime();
          
          if (!is_null($larp)) {
              $email->LarpId = $larp->Id;
              $campaign = $larp->getCampaign();
              if (!is_null($campaign)) {
                  $email->From = scrub($campaign->Email);
              }
          }
          $myName = $email->myName();
          if (empty($email->Subject)) $email->Subject = "Meddelande från $myName";
          
          if (!is_null($attachments) && !empty($attachments)) $email->SentAt = date_format($now,"Y-m-d H:i:s"); # Förhindra att det skickas innan bilagorna sparats färdigt.
          
          $now->modify("+$noOfDaysKept day");
          
          $email->DeletesAt = date_format($now,"Y-m-d H:i:s");
          
          $email->create();
          
          if (!is_null($attachments) && !empty($attachments)) {
              foreach ($attachments as $filename => $attachment) {
                  if (is_null($filename) || is_numeric($filename)) {
                      if (is_null($email->larp())) {
                          $filename = scrub("Berghems Vänner");
                      } else {
                          $filename = scrub($larp->Name);
                      }
                  }
                  if (!str_ends_with($filename,'.pdf')) $filename = $filename.'.pdf';
                  
                  Attachment::normalCreate($email, $filename, $attachment);
              }
              $email->SentAt = null;
              $email->update();
          }
          
          if (!$email->isKiR()) $email->sendNow();
          return $email;
          
      }
      
      
      
    //Ett mail som inte skickas eftersom personen inte vill ha epost 
      private static function createMessage($name, $greeting, $Subject, $Text, $senderText, $attachments, $noOfDaysKept, $larp, $senderId) {
        $email = self::newWithDefault();

        $email->To = "";
        $email->ToName = $name;
     
        $email->Greeting = $greeting;
        $email->Subject = $Subject;
        $email->Text = $Text;
        $email->SenderText = $senderText;
        $email->SenderPersonId = $senderId;

        $now = new Datetime();
        $email->SentAt = date_format($now,"Y-m-d H:i:s");

        $now->modify("+$noOfDaysKept day");
        $email->DeletesAt = date_format($now,"Y-m-d H:i:s");
        
        if (!is_null($larp)) {
            $email->LarpId = $larp->Id;
            $campaign = $larp->getCampaign();
            if (!is_null($campaign)) {
                $email->From = scrub($campaign->Email);
            }
        }
        $myName = $email->myName();
        if (empty($email->Subject)) $email->Subject = "Meddelande från $myName";
        
        
        
        $email->create();
        
        if (!is_null($attachments) && !empty($attachments)) {
            foreach ($attachments as $filename => $attachment) {
                if (is_null($filename) || is_numeric($filename)) {
                    if (is_null($email->larp())) {
                        $filename = scrub("Berghems Vänner");
                    } else {
                        $filename = scrub($larp->Name);
                    }
                }
                if (!str_ends_with($filename,'.pdf')) $filename = $filename.'.pdf';
                
                Attachment::normalCreate($email, $filename, $attachment);
            }
        }
        return $email;
        
    }
    
    public static function allBySelectedLARP(Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_email WHERE LarpId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allCommon() {
        $sql = "SELECT * FROM regsys_email WHERE LarpId IS NULL ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array());
    }
    
    public function toPerson() {
        $sql = "SELECT * FROM regsys_person WHERE ID IN (SELECT PersonId FROM regsys_email_person WHERE EmailId=?);";
        return Person::getOneObjectQuery($sql, array($this->Id));
    }
    
    
    
    public static function allForPersonAtLarp(Person $person, Larp $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_email WHERE LarpId = ? AND ID IN (SELECT EmailId FROM regsys_email_person WHERE PersonId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id, $person->Id));
    }
    
    public static function allForPerson(Person $person) {
        $sql = "SELECT * FROM regsys_email WHERE ID IN (SELECT EmailId FROM regsys_email_person WHERE PersonId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($person->Id));
    }
    
    
    public function getRecipients() {
        $sql = "SELECT * FROM regsys_person WHERE Id IN (SELECT PersonId FROM regsys_email_person WHERE EmailId=?);";
        return Person::getSeveralObjectsqQuery($sql, array($this->Id));
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
    public static function okToSendKiRNow() {
        # Först hittar vi tiden för när vi senast skickade något, om någonsin
        $sql = "SELECT * FROM regsys_email WHERE `SentAt` is not null AND `From` ='kontakt@kampeniringen.se' ORDER BY `SentAt` DESC LIMIT 1;";
        $lastestSentEmail = static::getOneObjectQuery($sql, array());
        if (is_null($lastestSentEmail)) return true;
        
        $now = new Datetime();
        $sent_at_date_time = new DateTime($lastestSentEmail->SentAt);
        $diff = $now->getTimestamp() - $sent_at_date_time->getTimestamp();
//         echo "<h1>$diff</h1>\n";
        if ($diff > 14) return true;
        return false;
    }
    
    private function isKiR() {
        if ($this->From =='kontakt@kampeniringen.se') return true;
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
        if (empty($this->ToName)) return "Kära deltagare";
        return trim(str_replace( array( '\'', '"', ',' , ';', '<', '>' ), ' ', $this->ToName));
//         return $this->ToName;
    }
    
    public function attachments() {
        return Attachment::allByEmail($this);
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_email SET SentAt=?, DeletesAt=?, SenderPersonId=?, LarpId=?, `From`=?, `To`=?, `Greeting`=?, `CC`=?, Subject=?, Text=?, SenderText=?, ErrorMessage=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->SentAt, $this->DeletesAt, $this->SenderPersonId, $this->LarpId, $this->From, $this->To, $this->Greeting, $this->CC, $this->Subject, 
                                  $this->Text, $this->SenderText, $this->ErrorMessage, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_email (SenderPersonId, LarpId, `From`, `To`, ToName, Greeting, CC, Subject, Text, SenderText, CreatedAt, DeletesAt, SentAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->SenderPersonId, $this->LarpId, $this->From, $this->To, $this->ToName, $this->Greeting, $this->CC, $this->Subject, $this->Text, $this->SenderText, date_format(new Datetime(),"Y-m-d H:i:s"), $this->DeletesAt, $this->SentAt))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Skapar ett standardmässigt HTML-meddelande. 
    # Så att alla taggar blir rätt och vi kan skicka ett alt-meddelande utan html-skräp så blilnda lättare kan läsa mailet.
    public function mailContent(?string $unsubscribeText="") {
        $larp = $this->larp();
        

        if (!is_null($larp)) {
            $campaign = $larp->getCampaign();
            if (!is_null($campaign))  $hej = $campaign->hej();
        }
        
        $greeting = $this->Greeting;
        if (empty($greeting)) $greeting = $hej." ".$this->receiverName();
        
        $senderText = $this->SenderText;
        if (is_null($larp)) {
            $title = "Brev från Berghems vänner";
            if (empty($senderText)) $senderText="Administratörerna";
        } else {
            $title = $larp->Name;
            if (empty($senderText)) $senderText="Arrangörerna av $title";
        }
        
     
        return "<!DOCTYPE html>
        <html>
        <head>
            <meta charset='utf-8'>
            <title>$title</title>
    	</head>
    	<body class='loggedin'>
            $greeting<br />
            <p>$this->Text</p>
            
            <br />
            <p>Med vänliga hälsningar<br /><br /><b>$senderText</b>
            $unsubscribeText
            </p>
        </body>";
    }
    

    public function sendNow() { 
        global $current_person;
        
        if (empty($this->To)) {
            $this->SentAt = $this->CreatedAt;
            $this->update();
            return;
        }
        
        //Create a new PHPMailer instance
        $mailer = new PHPMailer();
        $mailer->CharSet = 'UTF-8';
        
        //Set who the message is to be sent from
        $mailer->setFrom($this->From, $this->myName(),0);
//         $mail->setFrom($from, encode_utf_to_iso($myName)); # Tror faktiskt det ska vara så här
        //Set an alternative reply-to address
        $mailer->addReplyTo($this->From, $this->myName());
        //Set who the message is to be sent to
        
        
        
        //Om test, skicka bara till inloggad användare
        if (Environment::isTest()) {
            # Fixa så inga mail går iväg om man utvecklar
            if (isset($current_person)) {
                $mailer->addAddress($current_person->Email, $current_person->Name);
            } else {
                $mailer->addAddress("karin@tellen.se", "Karin Rappe");
            }
            
            
        } else {
        
            if (!($to_array = @unserialize($this->To))) {
                $mailer->addAddress($this->To, $this->receiverName());
                
                
            } elseif (!empty($to_array)) {
                foreach($to_array as $to) {
                    $mailer->addAddress($to, $this->receiverName());
                }
            }
    
            if (!empty($this->CC)) {
                $mailer->addCC($this->CC);
            }
        }
        

        
        $recipients = $this->getRecipients();
        $unsubscribeText = "";
        $host = Environment::getHost();
        if (sizeof($recipients) == 1) {
            $mailer->CharSet = 'UTF-8';
            $person = $recipients[0];
            $code = $person->getUnsubscribeCode();
            $unsubLink = $host.'/unsubscribe.php?personId='.$person->Id.'&code='.$code; 
            $mailer->addCustomHeader(
                'List-Unsubscribe',
                "<$unsubLink>"
                    );
            $mailer->addCustomHeader(
                'List-Unsubscribe-Post',
                'List-Unsubscribe=One-Click'
                );
            $unsubscribeText = "<br><br>Om du inte vill ha fler mail från oss kan du klicka på den här länken: <a href='$unsubLink'>Avstå från utskick</a>";
            
            
        }

        $mailer->Subject = $this->Subject;
        $mailer->AltBody = $this->Text;
        //Attach an image file
        // $mail->addAttachment('images/phpmailer_mini.png');
        
        $attachments = $this->attachments();
        
        if (!is_null($attachments) && !empty($attachments)) {
            foreach ($attachments as $attachment) {
                $mailer->AddStringAttachment($attachment->Attachement, $attachment->Filename, 'base64', 'application/pdf');
            }
        }
        
        
        $mailer->isHTML(true);

        $mailer->Body = $this->mailContent($unsubscribeText);
        
        //$mailer->SMTPDebug = true;
        
        if (str_contains($this->From, "kontakt@kampeniringen.se")) {
            $mailer->IsSMTP();
            $mailer->SMTPAuth = true;
            $mailer->SMTPSecure = "tls";
            $mailer->Host = "send.one.com";
            $mailer->Port = 587;
            $mailer->Username = $this->From;
            $mailer->Password = "BrestaBresta1125";   
                     
        }        
        
        /*
        Outgoing server name: mailout.one.com
        Port and encryption:
        - 587 with STARTTLS (recommended)
        - 465 with SSL/TLS
        - 25 with STARTTLS or none
        Authentication: your email address and password
        */
        
        
        if (!$mailer->send()) {
//             echo 'Mailer Error: ' . $mail->ErrorInfo;
            $this->ErrorMessage = $mailer->ErrorInfo;
            $this->update();
            return false;
        }
        
        $this->SentAt = date_format(new Datetime(),"Y-m-d H:i:s");
        $this->update();
        
        return true;
        
    }
    
    public static function handleEmailQueue() {
        //     return;
        Email::deleteOldMails();
        $current_queue = static::allUnsent();
        if (empty($current_queue)) return;
        if (!static::okToSendKiRNow()) return;
        //     echo "Send an email";
        $to_send = array_pop($current_queue); # Hämta äldsta mailet i kön
        return $to_send->sendNow();
    }
    
    public static function deleteOldMails() {
        $now = new Datetime();
        $sql = "DELETE FROM `regsys_email` WHERE Date(`DeletesAt`) < '".date_format($now,"Y-m-d")."';";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute()) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
        
    }
    
    public function connectToPerson($personId) {
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_email_person (EmailId, PersonId) VALUES (?,?);");
        if (!$stmt->execute(array($this->Id, $personId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
}
