<?php

class ShipType extends SelectionData{
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_group WHERE ShipTypeId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
    
}
