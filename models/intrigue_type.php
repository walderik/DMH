<?php

class IntrigueType extends SelectionData{
      

    public static function getIntrigeTypesForRole($roleId) {
        if (is_null($roleId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_role WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleId));
    }

    public static function getIntrigeTypesForLarpAndGroup($larpId, $groupId) {
        if (is_null($larpId) || is_null($groupId)) return array();
        
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_larp_group ".
            "WHERE LARP_GroupGroupId = ? AND LARP_GroupLARPId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($groupId, $larpId));
    }
    
    
    public static function countByTypeOnRoles(LARP $larp) {
        if (is_null($larp)) return Array();
        
        $type = strtolower(static::class)."Id";
        $type = static::class."Id";
        
        $sql = "select count(regsys_larp_role.RoleId) AS Num, regsys_intriguetype.Name AS Name FROM ".
            "regsys_larp_role, regsys_intriguetype_role, regsys_intriguetype WHERE ".
            "regsys_larp_role.larpId=? AND ".
            "regsys_larp_role.RoleId = regsys_intriguetype_role.RoleId AND ".
            "regsys_intriguetype.Id=regsys_intriguetype_role.IntrigueTypeId GROUP BY IntrigueTypeId";
        
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
            "regsys_larp_group, regsys_intriguetype_larp_group, regsys_intriguetype WHERE ".
            "regsys_larp_group.larpId=? AND ".
            "regsys_larp_group.LarpId=regsys_intriguetype_larp_group.LARP_GroupLARPId AND ".
            "regsys_larp_group.GroupId = regsys_intriguetype_larp_group.LARP_GroupGroupId AND ".
            "regsys_intriguetype.Id=regsys_intriguetype_larp_group.IntrigueTypeId GROUP BY IntrigueTypeId";
        
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