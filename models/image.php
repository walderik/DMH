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
    
    
    
    public static function newFromArray($post){
        $image = static::newWithDefault();
        $image->setValuesByArray($post);
        return $image;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['file_data'])) $this->file_data = $arr['file_data'];
        if (isset($arr['file_mime'])) $this->file_mime = $arr['file_mime'];
        if (isset($arr['file_name'])) $this->file_name = $arr['file_name'];
        if (isset($arr['Photographer'])) $this->Photographer = $arr['Photographer'];
     }
    
    
    
    
    
    
    public static function maySave($allow_pdf = false) {

        $allowed = array("jpg" => "image/jpg", "jpeg" => "image/jpeg", "gif" => "image/gif", "png" => "image/png");
        if ($allow_pdf) $allowed["pdf"] = "application/pdf";
        $filename = $_FILES["upload"]["name"];
        $filetype = $_FILES["upload"]["type"];
        $filesize = $_FILES["upload"]["size"];
        
        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) return "image_format";
        
        // Validate type of the file
        if(!in_array($filetype, $allowed)) return "image_format";
        
        // Validate file size - 0,5MB maximum
        $maxsize = 0.5 * 1024 * 1024;
        if($filesize > $maxsize) return "image_size";
        
        
    }
        
    # Create a new image in db
    public static function saveImage($filename="", $allow_pdf = false) {        
        $error = static::maySave($allow_pdf);
        if (isset($error)) return null;
        
        $file_mime = mime_content_type($_FILES["upload"]["tmp_name"]);
        
        if (empty($filename)) {
            $filename = $_FILES["upload"]["name"];
        } else {
            $filename = $filename.".".static::getExtension($file_mime);
        }
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_image (file_name, file_mime, file_data, Photographer) VALUES (?,?,?,?)");
        
        if (!$stmt->execute(array($filename,
            $file_mime,
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
    
     /*
    public static function loadById ($Id) {

        $connection = static::connectStatic();
        $stmt = $connection->prepare("SELECT Id, file_name, file_mime, file_data, Photographer ".
            "FROM regsys_image WHERE Id=?");
        
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
        $image->Id = $file["Id"];
        $image->file_data = $file["file_data"];
        $image->file_name = $file["file_name"];
        $image->file_mime = $file["file_mime"];
        $image->Photographer = $file["Photographer"];
        return $image;
    }
    
    */
    
    # Delete an image in db
    public function deleteImage($id) {
        $connection = $this->connect();
        $stmt = $connection->prepare("DELETE FROM regsys_image WHERE Id=?");
        
        if (!$stmt->execute(array($id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }

        $stmt = null;
        return;
    }
    
    
    public static function getAllPDFVerifications(LARP $larp) {
        $sql = "SELECT * FROM regsys_image WHERE file_mime='application/pdf' AND Id IN (".
            "SELECT ImageId FROM regsys_bookkeeping WHERE LarpId=?) ORDER BY Id";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    public static function getExtension($file_mime) {
        switch ($file_mime) {
            case "image/jpg":
            case "image/jpeg":
                return "jpg";
                break;
            case "image/gif":
                return "gif";
                break;
            case "image/png":
                return "png";
                break;
            case "application/pdf":
                return "pdf";
                break;
        }
        return "";
    }
    
    public static function update() {
        //Används bara vid anonymisering av databasen

        
        $file_mime = mime_content_type($_FILES["upload"]["tmp_name"]);
        $filename = "anonym";

        $connection = static::connectStatic();
        
        $stmt = $connection->prepare("UPDATE regsys_image SET file_name=?, file_mime=?, file_data=?, Photographer=? WHERE Id=?");
        
        if (!$stmt->execute(array($filename,
            $file_mime,
            file_get_contents($_FILES["upload"]["tmp_name"]),
            "",1))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $id = $connection->lastInsertId();
            $stmt = null;
            return $id;
    }
    
    
}
