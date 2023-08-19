<?php

class Attachment  extends BaseModel{
    
    public  $Id;
    public  $EmailId;
    public  $Filename;
    public  $Attachement;

    public static $orderListBy = 'EmailId';
    
    public static function newFromArray($post) {
        $attachment = static::newWithDefault();
        $attachment->setValuesByArray($post);
        return $attachment;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Attachement'])) $this->Attachement = $arr['Attachement'];
        if (isset($arr['Filename'])) $this->Filename = $arr['Filename'];
        if (isset($arr['EmailId'])) $this->EmailId = $arr['EmailId'];
        if (isset($arr['Id'])) $this->Id = $arr['Id'];  
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $attachment = new self();
        return $attachment;
    }
    
    # Normala sättet att lägga in attachments
    public static function normalCreate(Email $email, $filename, $the_attachment) {
        $scrubbed_filename = str_replace(array("'",'´', '"', '`'), '',$filename);
        $scrubbed_filename = mb_convert_encoding($scrubbed_filename, "ASCII");
        $attachment = Attachment::newWithDefault();
        $attachment->EmailId = $email->Id;
        $attachment->Filename = $scrubbed_filename;
        $attachment->Attachement = $the_attachment;
        $attachment->create();
        return $attachment;
    }
    
    public static function allByEmail(Email $email) {
        if (is_null($email)) return Array();
        $sql = "SELECT * FROM regsys_attachment WHERE EmailId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($email->Id));
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_attachment SET EmailId=?, Filename=?, Attachement=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->EmailId, $this->Filename, $this->Attachement, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt =  $connection->prepare("INSERT INTO regsys_attachment (EmailId, Filename, Attachement) VALUES (?, ?, ?)");
        
        if (!$stmt->execute(array($this->EmailId, $this->Filename, $this->Attachement))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }

}
