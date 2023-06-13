<?php
global $root;


include_once $root . '/includes/all_includes.php';

class Statistics  extends Dbh{
    
    
    public static function oldest(Larp $larp) {
        $sql = "SELECT socialsecuritynumber FROM regsys_person, regsys_registration WHERE ".
        "regsys_person.Id = regsys_registration.PersonId AND ".
        "LarpId=? AND NotComing=0 ORDER BY socialsecuritynumber ASC";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $ssn = $rows[0]['socialsecuritynumber'];
         return getAge(substr($ssn, 0, 8), $larp->StartDate);

    }
    
    public static function youngest(Larp $larp) {
        $sql = "SELECT socialsecuritynumber FROM regsys_person, regsys_registration WHERE ".
            "regsys_person.Id = regsys_registration.PersonId AND ".
            "LarpId=? AND NotComing=0 ORDER BY socialsecuritynumber DESC";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $ssn = $rows[0]['socialsecuritynumber'];
        
        
        return getAge(substr($ssn, 0, 8), $larp->StartDate);
        
    }
    
    public static function countHasPayed(LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId =? AND NotComing=0 AND AmountToPay <= AmountPayed;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    public static function countHasSpot(LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId =? AND NotComing=0 AND SpotAtLARP=1;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    public static function countParticipantHasSpot(LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId =? AND IsOfficial=0 AND NotComing=0 AND SpotAtLARP=1;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    public static function countOfficialHasSpot(LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId =? AND IsOfficial=1 AND NotComing=0 AND SpotAtLARP=1;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    public static function countIsMember(LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_registration WHERE LarpId =? AND NotComing=0 AND IsMember=1;";
        return static::countQuery($sql, array($larp->Id));
    }
    
    protected static function countQuery($sql, $var_array) {
        //Måste märka fältet som räkna med 'Num'
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute($var_array)) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return 0;
            
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;

        return $res[0]['Num'];
    }
    
}