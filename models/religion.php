<?php

class Religion extends SelectionData{
  
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_role WHERE ReligionId=?";
        return !static::existsQuery($sql, array($this->Id));    
    }
      
}
