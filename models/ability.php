<?php

class Ability extends SelectionData{
    public static function getAbilitiesForRole($roleId) {
        if (is_null($roleId)) return array();
        $sql = "SELECT * from regsys_ability WHERE Id IN ".
            "(SELECT AbilityId FROM regsys_ability_role WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleId));
    }
    
    public static function getAbilitiesForApprovedRoleCopy($roleCopyId) {
        if (is_null($roleCopyId)) return array();
        $sql = "SELECT * from regsys_ability WHERE Id IN ".
            "(SELECT AbilityId FROM regsys_ability_role_approved_copy WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleCopyId));
    }
    
    public function mayDelete() {
        $sql = "select count(RoleId) AS Num FROM regsys_ability_role WHERE AbilityId=?";
        return !static::existsQuery($sql, array($this->Id));
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
        
        $sql = "select count(regsys_larp_role.RoleId) AS Num, regsys_ability.Name AS Name FROM ".
            "regsys_larp_role, regsys_role, regsys_ability_role, regsys_ability WHERE ".
            "regsys_larp_role.larpId=? AND ".
            "regsys_larp_role.RoleId = regsys_ability_role.RoleId AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.NoIntrigue = 0 AND ".
            $mainStr .
            "regsys_ability.Id=regsys_ability_role.AbilityId GROUP BY AbilityId ORDER BY Num DESC";
            
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
    
     
    
}