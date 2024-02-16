<?php

class Race extends SelectionData{
  
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_role WHERE RaceId=?";
        return !static::existsQuery($sql, array($this->Id));
    }
    
}