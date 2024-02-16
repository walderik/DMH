<?php

class Ability extends SelectionData{
    public static function getAbilitiesForRole($roleId) {
        if (is_null($roleId)) return array();
        $sql = "SELECT * from regsys_ability WHERE Id IN ".
            "(SELECT AbilityId FROM regsys_ability_role WHERE ".
            "RoleId = ?) ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($roleId));
    }
    
    public function mayDelete() {
        $sql = "select count(RoleId) AS Num FROM regsys_ability_role WHERE AbilityId=?";
        return !static::existsQuery($sql, array($this->Id));
    }
    
    
    
}