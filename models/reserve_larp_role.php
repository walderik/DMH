<?php

class Reserve_LARP_Role extends BaseModel{
    
    public $Id;
    public $LARPId;
    public $RoleId;
    public $IsMainRole = 0;
    public $IntrigueIdeas;
    
    public static $orderListBy = 'RoleId';
    
    public static function newFromArray($post){
        $larp_role = static::newWithDefault();
        if (isset($post['Id']))   $larp_role->Id = $post['Id'];
        if (isset($post['LARPId'])) $larp_role->LARPId = $post['LARPId'];
        if (isset($post['RoleId'])) $larp_role->RoleId = $post['RoleId'];
        if (isset($post['IsMainRole'])) $larp_role->IsMainRole = $post['IsMainRole'];
        if (isset($post['IntrigueIdeas'])) $larp_role->IntrigueIdeas = $post['IntrigueIdeas'];
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
        $stmt = $this->connect()->prepare("UPDATE regsys_reserve_larp_role SET IsMainRole=?, IntrigueIdeas=?  WHERE LARPId=? AND RoleId=?;");
        
        if (!$stmt->execute(array($this->IsMainRole, $this->IntrigueIdeas, $this->LARPId, $this->RoleId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_reserve_larp_role (LARPId, RoleId, IsMainRole, IntrigueIdeas) VALUES (?,?,?,?);");
        
        if (!$stmt->execute(array($this->LARPId, $this->RoleId, $this->IsMainRole, $this->IntrigueIdeas))) {
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
    
    
    
    # Hämta intrigtyperna
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigeTypesForReserveRole($this->Id);
    }
    
    public function getSelectedIntrigueTypeIds() {
        $stmt = $this->connect()->prepare("SELECT IntrigueTypeId FROM  regsys_intriguetype_reserve_role WHERE ReserveLarpRoleId = ? ORDER BY IntrigueTypeId;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = $row['IntrigueTypeId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    
    
    public function saveAllIntrigueTypes($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_reserve_role (IntrigueTypeId, ReserveLarpRoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    
    public function deleteAllIntrigueTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_reserve_role WHERE ReserveLarpRoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public static function delete($id)
    {
        $larp_role = static::loadById($id);
        
        $larp_role->deleteAllIntrigueTypes();
        
        parent::delete($id);
    }
    
    
    
}