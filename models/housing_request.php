<?php

class HousingRequest extends SelectionData{
    
    public function mayDelete() {
        $sql = "select count(GroupId) AS Num FROM regsys_larp_group WHERE HousingRequestId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        $sql = "select count(Id) AS Num FROM regsys_registration WHERE HousingRequestId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        $sql = "select count(Id) AS Num FROM regsys_reserve_registration WHERE HousingRequestId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
    
}