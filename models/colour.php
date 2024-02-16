<?php

class Colour extends SelectionData{
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_group WHERE ColourId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
    
}
