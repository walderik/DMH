<?php

class Belief extends SelectionData{
  
    
    public function mayDelete() {
        $sql = "select count(Id) AS Num FROM regsys_role WHERE BeliefId=?";
        return !static::existsQuery($sql, array($this->Id));    
    }
      
}
