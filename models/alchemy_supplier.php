<?php

class Alchemy_Supplier extends BaseModel{
    
    public $Id;
    public $RoleId;
    public $Workshop;
    public $OrganizerNotes;
    
    public static $orderListBy = 'RoleId';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['Workshop'])) $this->Workshop = $arr['Workshop'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        
        if (isset($this->Workshop) && ($this->Workshop=='0000-00-00' || $this->Workshop=='')) $this->Workshop = null;
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_supplier SET RoleId=?, Workshop=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->RoleId, $this->Workshop, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_alchemy_supplier (RoleId, Workshop, OrganizerNotes) VALUES (?,?,?);");
        
        if (!$stmt->execute(array($this->RoleId, $this->Workshop, $this->OrganizerNotes))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    public static function getForRole(Role $role) {
        if (empty($role)) return null;
        $sql = "SELECT * FROM regsys_alchemy_supplier WHERE RoleId=?";
        return static::getOneObjectQuery($sql, array($role->Id));
        
    }
    
    public static function isSupplier(Role $role) {
        if (empty($role)) return null;
        if (is_null(static::getForRole($role))) return false;
        return true;
    }
    
    
    
    public function getRole() {
        if (empty($this->RoleId)) return null;
        return Role::loadById($this->RoleId);
    }
    
    public function hasDoneWorkshop() {
        if (empty($this->Workshop)) return false;
        return true;
    }
    
    public function numberOfIngredientsApproved(LARP $larp) {
        //TODO kolla att antalet ingredienser är godkända
        return false;
    }
    
    

    public static function delete($id)
    {
        //$supplier = static::loadById($id);
        
        //TODO ta bort hur mycket de har haft med på tidigare lajv
        parent::delete($id);
    }
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_supplier WHERE RoleId In (
            SELECT Id FROM regsys_role WHERE CampaignId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function createSuppliers($roleIds, LARP $larp) {
        //Ta reda på vilka som inte redan är magiker
        $exisitingRoleIds = array();
        $suppliers = static::allByCampaign($larp);
        foreach ($suppliers as $supplier) {
            $exisitingRoleIds[] = $supplier->RoleId;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $supplier = Alchemy_Supplier::newWithDefault();
            $supplier->RoleId = $roleId;
            $supplier->create();
        }
    }
    
    
}