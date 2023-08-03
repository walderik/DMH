<?php

class IntrigueType extends SelectionData{
      

    public static function getIntrigeTypesForRole($roleId) {
        if (is_null($roleId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_role WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleId));
    }

    public static function getIntrigueTypesForIntrigue($intrigueId) {
        if (is_null($intrigueId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_intrigue WHERE ".
            "IntrigueId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($intrigueId));
    }
    
    public static function getIntrigeTypesForGroup($groupId) {
        if (is_null($larpId) || is_null($groupId)) return array();
        
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_group ".
            "WHERE GroupId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($groupId));
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
            "regsys_larp_group.GroupId = regsys_intriguetype_group.GroupId AND ".
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
    
    
    
    
}