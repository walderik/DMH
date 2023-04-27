<?php
global $root;


include_once $root . '/includes/all_includes.php';

class Statistics  extends Dbh{
    
    
    public static function oldest(Larp $larp) {
        $sql = "SELECT socialsecuritynumber FROM regsys_person, regsys_registration WHERE ".
        "regsys_person.Id = regsys_registration.PersonId AND ".
        "LarpId=? ORDER BY socialsecuritynumber ASC";
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
        

        $birthday = DateTime::createFromFormat('Ymd', substr($ssn, 0, 8));
        
        $larpStartDate = DateTime::createFromFormat('Y-m-d', substr($larp->StartDate, 0, 10));
        
        
        $interval = date_diff($birthday, $larpStartDate);
        return $interval->format('%Y');

    }
    
    public static function youngest(Larp $larp) {
        $sql = "SELECT socialsecuritynumber FROM regsys_person, regsys_registration WHERE ".
            "regsys_person.Id = regsys_registration.PersonId AND ".
            "LarpId=? ORDER BY socialsecuritynumber DESC";
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
        
        
        $birthday = DateTime::createFromFormat('Ymd', substr($ssn, 0, 8));
        
        $larpStartDate = DateTime::createFromFormat('Y-m-d', substr($larp->StartDate, 0, 10));
        
        
        $interval = date_diff($birthday, $larpStartDate);
        return $interval->format('%Y');
        
    }
    
    
}