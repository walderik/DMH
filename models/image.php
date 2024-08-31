<?php

class Image extends BaseModel{
    public $Id;
    public $file_name;
    public $file_mime;
    public $file_data;
    public $Photographer;
    
    public static $orderListBy = 'Id';
    
    const MAXSIZE = 0.5 * 1024 * 1024;
    
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
        
        $file_mime = mime_content_type($_FILES["upload"]["tmp_name"]);

        // Validate file extension
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        if(!array_key_exists($ext, $allowed)) return "image_format";
        
        // Validate type of the file
        if(!in_array($filetype, $allowed)) return "image_format";

        // Validate type of the file
        if(!in_array($file_mime, $allowed)) return "image_format";
        
    }
    
    
    private static function compressImage($source) {
        $quality = 75;
        
        // Temporary path
        $uploadPath = dirname($source);
        
        // File info
        //$fileName = basename($_FILES["upload"]["tmp_name"]);
        $compressedImageName = $uploadPath . "/tmpImage.jpg";
        
        
        // Get image info
        $imgInfo = getimagesize($source);
        $mime = $imgInfo['mime'];
        
        // Create a new image from file and then compress it
        switch($mime){
            case "image/jpg":
            case "image/jpeg":
                $image = imagecreatefromjpeg($source);
                break;
            case 'image/png':
                $image = imagecreatefrompng($source);
                break;
            case 'image/gif':
                $image = imagecreatefromgif($source);
                break;
        }
        
        do {
            imagejpeg($image, $compressedImageName, $quality);
            clearstatcache();
            $quality = $quality - 10;
        }
        while (filesize($compressedImageName) > static::MAXSIZE && $quality > 15);

        // Return compressed image
        return $compressedImageName;
    }
    
        
    # Create a new image in db
    public static function saveImage($filename="", $allow_pdf = false) {        
        $error = static::maySave($allow_pdf);
        if (isset($error)) return null;
        
        $file_mime = mime_content_type($_FILES["upload"]["tmp_name"]);
        
        
        //$file_data = file_get_contents($_FILES["upload"]["tmp_name"]);
        $filesize = $_FILES["upload"]["size"];
        
        if ($filesize > static::MAXSIZE && static::isImage($file_mime)) {
            $compressedImage = static::compressImage($_FILES["upload"]["tmp_name"]);
            // Get image info
            $imgInfo = getimagesize($compressedImage);
            $file_mime = $imgInfo['mime'];
            
            $file_data = file_get_contents($compressedImage);
        }
        else {
            $file_data = file_get_contents($_FILES["upload"]["tmp_name"]);
        }
        
        
        if (empty($filename)) {
            $filename = $_FILES["upload"]["tmp_name"];
        } else {
            $filename = $filename.".".static::getExtension($file_mime);
        }
        
        
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_image (file_name, file_mime, file_data, Photographer) VALUES (?,?,?,?)");
        
        if (!$stmt->execute(array($filename,
            $file_mime,
            $file_data, 
            $_POST['Photographer']))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $id = $connection->lastInsertId();
        $stmt = null;
        return $id;
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
    
    
    
    public static function isImage($file_mime) {
        switch ($file_mime) {
            case "image/jpg":
            case "image/jpeg":
            case "image/gif":
            case "image/png":
                return true;
                break;
            case "application/pdf":
                return false;
                break;
        }
        return false;
    }
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_image SET file_name=?,
            file_mime=?, file_data=?, Photographer=?  WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->file_name, $this->file_mime, $this->file_data,
            $this->Photographer, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
}
