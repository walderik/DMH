<?php

class GroupType extends SelectionData{
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_group WHERE GroupTypeId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
    
}
