<?php

class RoleFunction extends SelectionData{
    public static function getRoleFunctionsForRole($roleId) {
        if (is_null($roleId)) return array();
        $sql = "SELECT * from regsys_rolefunction WHERE Id IN ".
            "(SELECT RoleFunctionId FROM regsys_rolefunction_role WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleId));
    }
    
    public static function getRoleFunctionsForApprovedRoleCopy($roleCopyId) {
        if (is_null($roleCopyId)) return array();
        $sql = "SELECT * from regsys_rolefunction WHERE Id IN ".
            "(SELECT RoleFunctionId FROM regsys_rolefunction_role_approved_copy WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleCopyId));
    }
    
    public function mayDelete() {
        $sql = "select count(RoleId) AS Num FROM regsys_rolefunction_role WHERE RoleFunctionId=?";
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
        
        $sql = "select count(regsys_larp_role.RoleId) AS Num, regsys_rolefunction.Name AS Name FROM ".
            "regsys_larp_role, regsys_role, regsys_rolefunction_role, regsys_rolefunction WHERE ".
            "regsys_larp_role.larpId=? AND ".
            "regsys_larp_role.RoleId = regsys_rolefunction_role.RoleId AND ".
            "regsys_larp_role.RoleId = regsys_role.Id AND ".
            "regsys_role.NoIntrigue = 0 AND ".
            $mainStr .
            "regsys_rolefunction.Id=regsys_rolefunction_role.RoleFunctionId GROUP BY RoleFunctionId ORDER BY Num DESC";
            
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
