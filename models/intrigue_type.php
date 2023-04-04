<?php

class IntrigueType extends SelectionData{
      

    public static function getIntrigeTypesForLarpAndRole($larpId, $roleId) {
        if (is_null($larpId) || is_null($roleId)) return array();
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_larp_role WHERE LARP_RoleLARPid = ? ".
            "AND LARP_RoleRoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larpId, $roleId));
    }

    public static function getIntrigeTypesForLarpAndGroup($larpId, $groupId) {
        if (is_null($larpId) || is_null($groupId)) return array();
        
        $sql = "SELECT * from regsys_intriguetype WHERE Id IN ".
            "(SELECT IntrigueTypeId FROM regsys_intriguetype_larp_group ".
            "WHERE LARP_GroupGroupId = ? AND LARP_GroupLARPId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($groupId, $larpId));
    }
    
}