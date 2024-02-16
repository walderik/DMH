<?php

class Wealth extends SelectionData{
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_group WHERE WealthId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        $sql = "select count(Id) AS Num FROM regsys_role WHERE WealthId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
    
}
