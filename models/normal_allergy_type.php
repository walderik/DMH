<?php

class NormalAllergyType extends SelectionDataGeneral{
    
//     public static $tableName = 'normalallergytype';

    public static function allOnLarp(LARP $larp) {
        $sql = "SELECT * FROM regsys_normalallergytype WHERE Id  IN ".
            "(SELECT NormalAllergyTypeId FROM regsys_normalallergytype_person, regsys_registration WHERE ".
            "regsys_registration.LarpId = ? AND ".
            "regsys_registration.NotComing = 0 AND ".
            "regsys_registration.PersonId = regsys_normalallergytype_person.PersonId) ORDER BY ".static::$orderListBy.";";

        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
        
        
    }
      
}