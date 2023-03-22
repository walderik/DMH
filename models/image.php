<?php

class Image extends BaseModel{
    public $Id;
    public $file_name;
    public $file_mime;
    public $file_data;
    public $Photographer;
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function maySave() {
        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        $filename = $_FILES["upload"]["name"];
        $filetype = $_FILES["upload"]["type"];
        $filesize = $_FILES["upload"]["size"];
        
        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) return "Fel format på filen. Var vänlig välj en fil med ett av de godkända formaten.";
        
        // Validate type of the file
        if(!in_array($filetype, $allowed)) "Fel format på filen. Var vänlig välj en fil med ett av de godkända formaten.";
        
        // Validate file size - 0,5MB maximum
        $maxsize = 0.5 * 1024 * 1024;
        if($filesize > $maxsize) return "Filen är för stor. Minska ner den i ett bildhanteringsprogram.";
        
        
    }
        
    # Create a new image in db
    public static function saveImage() {
        global $tbl_prefix;
        
        $error = static::maySave();
        if (isset($error)) return null;
        
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO ".$tbl_prefix."image (`file_name`, `file_mime`, `file_data`, Photographer) VALUES (?,?,?,?)");
        
        if (!$stmt->execute(array($_FILES["upload"]["name"],
            mime_content_type($_FILES["upload"]["tmp_name"]),
            file_get_contents($_FILES["upload"]["tmp_name"]), 
            $_POST['Photographer']))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $id = $connection->lastInsertId();
        $stmt = null;
        return $id;
    }
    
     
    public static function loadById ($Id) {
        global $tbl_prefix;
        $connection = static::connectStatic();
        $stmt = $connection->prepare("SELECT file_name, `file_mime`, `file_data`, Photographer FROM `regsys_image` WHERE `Id`=?");
        
        if (!$stmt->execute(array($Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
        $file = $stmt->fetch();
                
        if ($file===false) {
            echo "$Id not found";
            return false;
        }
        $image = Image::newWithDefault();
        $image->file_data = $file["file_data"];
        $image->file_name = $file["file_name"];
        $image->file_mime = $file["file_mime"];
        $image->Photographer = $file["Photographer"];
        return $image;
    }
    
    # Create a new image in db
    public function deleteImage($id) {
        global $tbl_prefix;
        
        $connection = $this->connect();
        $stmt = $connection->prepare("DELETE FROM ".$tbl_prefix."image WHERE Id=?");
        
        if (!$stmt->execute(array($Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
        return;
    }
    
    
}
