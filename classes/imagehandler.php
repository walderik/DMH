<?php

class Image extends BaseModel{
    /*
    public $Id;
    public $file_name;
    public $file_mime;
    public $file_data;
    
    public static $orderListBy = 'file_name';
    
    public static function newFromArray($post){
        $imagehandler = static::newWithDefault();
        if (isset($post['Id'])) $imagehandler->Id = $post['Id'];
        if (isset($post['file_name'])) $imagehandler->file_name = $post['file_name'];
        if (isset($post['file_mime'])) $imagehandler->file_mime = $post['file_mime'];
        if (isset($post['file_data'])) $imagehandler->file_data = $post['file_data'];

        
        return $imagehandler;
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    */
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
        
    # Create a new image in db
    public function saveImage() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO ".$tbl_prefix."image (`file_name`, `file_mime`, `file_data`) VALUES (?,?,?)");
        
        if (!$stmt->execute(array($_FILES["upload"]["name"],
            mime_content_type($_FILES["upload"]["tmp_name"]),
            file_get_contents($_FILES["upload"]["tmp_name"])))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
     
    // (E) LOAD FILE FROM DATABASE
    public function load ($Id) {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("SELECT file_name, `file_mime`, `file_data` FROM `regsys_image` WHERE `Id`=?");
        
        if (!$stmt->execute(array($Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
        $file = $stmt->fetch();
        
        // (E2) FILE NOT FOUND
        if ($file===false) {
            echo "$Id not found";
            return false;
        }
        
        // (E3) OUTPUT FILE
        // header("Content-type: " . $file["file_mime"]);
        //header("Content-Type: application/octet-stream");
        //header("Content-Transfer-Encoding: Binary");
        //header("Content-disposition: attachment; filename=\"". $file["file_name"] ."\"");
        return $file["file_data"];
    }
    
    
}
