<?php

class LarperType extends SelectionData{
    
//     public static $tableName = 'larpertype';

    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_role WHERE LarperTypeId=?";
        return !static::existsQuery($sql, array($this->Id));
    }
    
}