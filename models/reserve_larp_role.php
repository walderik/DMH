<?php

class Reserve_LARP_Role extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $RoleId;
    public $IsMainRole = 0;
    
    public static $orderListBy = 'RoleId';
    
    public static function newFromArray($post){
        $larp_role = static::newWithDefault();
        if (isset($post['Id']))   $larp_role->Id = $post['Id'];
        if (isset($post['LARPId'])) $larp_role->LARPId = $post['LARPId'];
        if (isset($post['RoleId'])) $larp_role->RoleId = $post['RoleId'];
        if (isset($post['IsMainRole'])) $larp_role->IsMainRole = $post['IsMainRole'];
        return $larp_role;
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function isReserve($roleId, $larpId) {
        $sql = "SELECT * FROM regsys_reserve_larp_role WHERE RoleId = ? AND LARPId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($roleId, $larpId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            return false;
        }
        
        return true;
    }
        
    
    # Hämta relationen baserat på en roll på ett visst lajv
    //     public static function getByLarpAndRole($larpId, $roleId){
    public static function loadByIds($roleId, $larpId)
    {
        
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_reserve_larp_role WHERE RoleId = ? AND LARPId = ?";
        return static::getOneObjectQuery($sql, array($roleId, $larpId));
    }
    
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_reserve_larp_role SET IsMainRole=? WHERE LARPId=? AND RoleId=?;");
        
        if (!$stmt->execute(array($this->IsMainRole, $this->LARPId, $this->RoleId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_reserve_larp_role (LARPId, RoleId, IsMainRole) VALUES (?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->RoleId, $this->IsMainRole))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    # returnera en array med alla karaktärer som är anmälda till lajvet
    public static function getReserveRolesForPerson($larpId, $personId) {
        if (is_null($larpId)) return Array();
        $sql = "SELECT * FROM regsys_reserve_larp_role WHERE LARPId = ? AND RoleId IN ".
        "(SELECT Id FROM regsys_role WHERE PersonId =?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larpId, $personId));
    }
    
    
    public static function deleteByIds($larpId, $roleId) {
        $stmt = $this->connect()->prepare("DELETE FROM ".
            "regsys_reserve_larp_role WHERE LARPId = ? AND RoleId = ?;");
        if (!$stmt->execute(array($larpId, $roleId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
}