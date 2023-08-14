<?php

class Housing extends BaseModel{
    
    public $LARPId;
    public $PersonId;
    public $HouseId;
    
    public static $orderListBy = 'PersonId';
    
    public static function newFromArray($post){
        $housing = static::newWithDefault();
        if (isset($post['LARPId'])) $housing->LARPId = $post['LARPId'];
        if (isset($post['PersonId'])) $housing->PersonId = $post['PersonId'];
        if (isset($post['HouseId'])) $housing->HouseId = $post['HouseId'];
        echo "New housing: ";
        print_r($housing);
        echo "<br>";
        return $housing;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_housing (LARPId, PersonId, HouseId) VALUES (?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->PersonId, $this->HouseId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    public static function deleteHousing($larpid, $personid) {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_housing WHERE LARPId=? AND PersonId=?");
        
        if (!$stmt->execute(array($larpid, $personid))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public static function getHousing(Person $person, Larp $larp) {
        $sql = "SELECT * FROM regsys_housing WHERE LARPId=? AND PersonId=?";
        static::getOneObjectQuery($sql, array($larp->Id, $person->Id));
    }
}