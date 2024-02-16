<?php

class Council extends SelectionData{
 
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_role WHERE CouncilId=?";
        return !static::existsQuery($sql, array($this->Id));
    }
    
}