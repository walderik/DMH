<?php

class Guard extends SelectionData{

    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_role WHERE GuardId=?";
        return !static::existsQuery($sql, array($this->Id));
    }
    
}