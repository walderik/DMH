<?php

class IntrigueType extends SelectionData{
    public $ForRole = 1;
    public $ForGroup = 1;
    
    
    public function setValuesByArray($arr) {
        parent::setValuesByArray($arr);

        if (isset($arr['ForRole'])) {
            if ($arr['ForRole'] == "on" || $arr['ForRole'] == 1) {
                $this->ForRole = 1;
            }
            else {
                $this->ForRole = 0;
            }
        }
        else {
            $this->ForRole = 0;
        }
        
        if (isset($arr['ForGroup'])) {
            if ($arr['ForGroup'] == "on" || $arr['ForGroup'] == 1) {
                $this->ForGroup = 1;
            }
            else {
                $this->ForGroup = 0;
            }
        }
        else {
            $this->ForGroup = 0;
        }
    }
    
    
    # Update an existing object in db
    public function update() {
        parent::update();
        $this->setSpecial();
    }
    
    # Create a new object in db
    public function create() {
        parent::create();
        $this->setSpecial();
    }
    
    private function setSpecial() {

        $stmt = $this->connect()->prepare("UPDATE regsys_".strtolower(static::class)." SET ForRole=?, ForGroup=? WHERE id = ?");
        
        if (!$stmt->execute(array($this->ForRole, $this->ForGroup, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    

    public static function getIntrigeTypesForRole($larproleId) {
        if (is_null($larproleId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_role WHERE ".
            "LarpRoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larproleId));
    }

    public static function getIntrigeTypesForReserveRole($reservelarproleId) {
        if (is_null($reservelarproleId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_reserve_role WHERE ".
            "ReserveLarpRoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($reservelarproleId));
    }
    
    public static function getIntrigueTypesForIntrigue($intrigueId) {
        if (is_null($intrigueId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_intrigue WHERE ".
            "IntrigueId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($intrigueId));
    }
    
    public static function getIntrigeTypesForGroup($larpgroupId) {
        if (is_null($larpgroupId)) return array();
        
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_group ".
            "WHERE LarpGroupId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larpgroupId));
    }
    
    
    public static function countByTypeOnRoles(LARP $larp, $mainRole) {
        if (is_null($larp)) return Array();
        
        if ($mainRole) {
            $mainStr = "regsys_larp_role.IsMainRole=1 AND ";
        }
        else {
            $mainStr = "regsys_larp_role.IsMainRole=0 AND ";
        }
        
        
        $type = strtolower(static::class)."Id";
        $type = static::class."Id";
        
        $sql = "select count(regsys_larp_role.RoleId) AS Num, regsys_intriguetype.Name AS Name FROM ".
            "regsys_larp_role, regsys_role, regsys_intriguetype_role, regsys_intriguetype WHERE ".
            "regsys_larp_role.larpId=? AND ".
            "regsys_larp_role.RoleId = regsys_intriguetype_role.RoleId AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.NoIntrigue = 0 AND ".
            $mainStr .
            "regsys_intriguetype.Id=regsys_intriguetype_role.IntrigueTypeId GROUP BY IntrigueTypeId ORDER BY Num DESC";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(Array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $rows;
        
        
    }
    
    public static function countByTypeOnGroups(LARP $larp) {
        if (is_null($larp)) return Array();
        
        $type = strtolower(static::class)."Id";
        $type = static::class."Id";
        
        $sql = "select count(regsys_larp_group.GroupId) AS Num, regsys_intriguetype.Name AS Name FROM ".
            "regsys_larp_group, regsys_intriguetype_group, regsys_intriguetype WHERE ".
            "regsys_larp_group.larpId=? AND ".
            "regsys_larp_group.Id = regsys_intriguetype_group.LarpGroupId AND ".
            "regsys_intriguetype.Id=regsys_intriguetype_group.IntrigueTypeId GROUP BY IntrigueTypeId";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(Array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $rows;
        
        
    }
    
    public function mayDelete() {
        $sql = "select count(LarpGroupId) AS Num FROM regsys_intriguetype_group WHERE IntrigueTypeId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        $sql = "select count(RoleId) AS Num FROM regsys_intriguetype_role WHERE IntrigueTypeId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
   
    public function getForString() {
        $for = array();
        if ($this->ForRole == 1) $for[] = "Karaktärer";
        if ($this->ForGroup == 1) $for[] = "Grupper";
        return implode(", ", $for);
    }
    
    public static function allActiveForGroup(LARP $larp) {
        $sql = "SELECT * FROM regsys_".strtolower(static::class)." WHERE active = 1 AND ForGroup=1 AND CampaignId=? ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function allActiveForRole(LARP $larp) {
        $sql = "SELECT * FROM regsys_".strtolower(static::class)." WHERE active = 1 AND ForRole=1 AND CampaignId=? ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    
    # En dropdown där man kan välja den här
    public static function selectionDropdownGroup(LARP $larp, ?bool $multiple=false, ?bool $required=true, $selected=null) {
        $selectionDatas = static::allActiveForGroup($larp);
        if (empty($selectionDatas)) return;
        selectionByArray(static::class , $selectionDatas, $multiple, $required, $selected);
    }
    
    # En dropdown där man kan välja den här
    public static function selectionDropdownRole(LARP $larp, ?bool $multiple=false, ?bool $required=true, $selected=null) {
        $selectionDatas = static::allActiveForRole($larp);
        if (empty($selectionDatas)) return;
        selectionByArray(static::class , $selectionDatas, $multiple, $required, $selected);
    }
    
    # Används den här tabellen
    public static function isInUseForGroup(LARP $larp) {
         $sql ="SELECT Count(Id) as Num FROM regsys_".strtolower(static::class)." WHERE active = 1 AND ForGroup=1 AND CampaignId = ? ORDER BY SortOrder LIMIT 1;";
        return static::existsQuery($sql, array($larp->CampaignId));
    }
 
    # Används den här tabellen
    public static function isInUseForRole(LARP $larp) {
        $sql ="SELECT Count(Id) as Num FROM regsys_".strtolower(static::class)." WHERE active = 1 AND ForRole=1 AND CampaignId = ? ORDER BY SortOrder LIMIT 1;";
        return static::existsQuery($sql, array($larp->CampaignId));
    }
    
    
}