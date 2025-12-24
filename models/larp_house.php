<?php

class Larp_House extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $HouseId;
    public $OrganizerComment;
    public $PublicComment;
    
    public static $orderListBy = 'HouseId';
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        if (isset($post['Id'])) $object->Id = $post['Id'];
        if (isset($post['LARPId'])) $object->LARPId = $post['LARPId'];
        if (isset($post['HouseId'])) $object->HouseId = $post['HouseId'];
        if (isset($post['OrganizerComment'])) $object->OrganizerComment = $post['OrganizerComment'];
        if (isset($post['PublicComment'])) $object->PublicComment = $post['PublicComment'];
        return $object;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_larp_house (LARPId, HouseId, OrganizerComment, PublicComment) VALUES (?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->HouseId, $this->OrganizerComment, $this->PublicComment))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_larp_house SET OrganizerComment=?, PublicComment=? WHERE Id=?;");
        
        if (!$stmt->execute(array($this->OrganizerComment, $this->PublicComment, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public static function loadByIds($house, $larpId) {
        $sql = "SELECT * FROM regsys_larp_house WHERE HouseId = ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($house, $larpId));
    }
    
    public function getLarp() {
        return LARP::loadById($this->LARPId);
    }
    
    public function getHouse() {
        return House::loadById($this->HouseId);
    }
    
}