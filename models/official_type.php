<?php

class OfficialType extends SelectionData{
    
    public function mayDelete() {
        $sql = "select count(Reserve_RegistrationId) AS Num FROM regsys_officialtype_reserve WHERE OfficialTypeId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        $sql = "select count(RegistrationId) AS Num FROM regsys_officialtype_person WHERE OfficialTypeId=?";
        $exists = static::existsQuery($sql, array($this->Id));
        if ($exists) return false;
        return true;
    }
    
}