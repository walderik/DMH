<?php

class AccessControl extends Dbh {
    const ADMIN = 0;
    const BOARD = 1;
    const HOUSES = 2;
    
    const ACCESS_TYPES = [
        AccessControl::ADMIN => "OM Admin",
        AccessControl::BOARD => "Styrelse",
        AccessControl::HOUSES => "Hus & Läger"
    ];
    
    
   
    public static function accessControlCampaign() {
        global $current_person, $current_larp;
        if (empty($current_larp) or (empty($current_person))) {
            header('Location: ../index.php');
            exit;
        }
        //If the person has admin privielieges they may always see
        if (static::hasAccessOther($current_person, AccessControl::ADMIN)) return;
        
        if (static::hasAccessCampaign($current_person, $current_larp->CampaignId)) return;
    
        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
    }

    public static function accessControlLarp() {
        global $current_person, $current_larp;
        if (empty($current_larp) or (empty($current_person))) {
            header('Location: ../index.php');
            exit;
        }
        //If the person has admin privielieges they may always see
        if (static::hasAccessOther($current_person, AccessControl::ADMIN)) return;

        if (static::hasAccessLarp($current_person, $current_larp)) return;

        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
    }
    
    public static function accessControlOther($access) {
        global $current_person, $current_larp;
        if (empty($current_larp) or (empty($current_person))) {
            header('Location: ../index.php');
            exit;
        }
        //If the person has admin privielieges they may always see
        if (static::hasAccessOther($current_person, AccessControl::ADMIN)) return;
        
        if (static::hasAccessOther($current_person, $access)) return;
        
        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
        
    }
    
    public static function isMoreThanParticipant(Person $person, LARP $larp) {
        if (static::hasAccessLarp($person, $larp)) return true;
        foreach (AccessControl::ACCESS_TYPES as $key => $value) {
            if (static::hasAccessOther($person, $key)) return true;
        }
        return false;
    }
        
    public static function hasAccessCampaign(Person $person, $campaignId) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_campaign WHERE ".
            "regsys_access_control_campaign.PersonId = ? AND ".
            "CampaignId=?;";
        return static::existsQuery($sql, array($person->Id, $campaignId));
    }
    
    public static function hasAccessLarp(Person $person, LARP $larp) {
        if (static::hasAccessCampaign($person, $larp->CampaignId)) return true;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_larp WHERE ".
            "regsys_access_control_larp.PersonId = ? AND ".
            "LarpId=?;";
        return static::existsQuery($sql, array($person->Id, $larp->Id));
        
    }
    
    public static function hasAccessOther(Person $person, int $access) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_other WHERE ".
            "regsys_access_control_other.PersonId = ? AND ".
            "(Permission=?);";
        
        return static::existsQuery($sql, array($person->Id, $access));
        
    }
    
    public static function grantCampaign($personId, $campaignId) { 
        if (empty($personId)) return;
        $person = Person::loadById($personId);
        if (AccessControl::hasAccessCampaign($person, $campaignId)) return;
            
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_campaign (PersonId, CampaignId) VALUES (?,?)");
        
        if (!$stmt->execute(array($personId, $campaignId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $id = $connection->lastInsertId();
            $stmt = null;
            return $id;
    }
    
    
    public static function revokeCampaign($personId, $campaignId)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_campaign WHERE PersonId=? AND CampaignId=?");
        
        if (!$stmt->execute(array($personId, $campaignId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
 
    public static function grantLarp(Person $person, Larp $larp) {
        if (AccessControl::hasAccessLarp($person, $larp)) return;
            
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_larp (PersonId, LarpId) VALUES (?,?)");
        
        if (!$stmt->execute(array($person->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $id = $connection->lastInsertId();
        $stmt = null;
        return $id;
    }
    
    
    public static function revokeLarp($personId, $larpId) 
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_larp WHERE PersonId=? AND LarpId=?");
        
        if (!$stmt->execute(array($personId, $larpId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
 
    public static function grantOther($personId, $access) {
        if (empty($personId)) return;
        $person = Person::loadById($personId);
        if (static::hasAccessOther($person, $access)) return;
        
        $connection = static::connectStatic();
        
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_other (PersonId, Permission) VALUES (?,?)");
        
        if (!$stmt->execute(array($personId, $access))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $id = $connection->lastInsertId();
        $stmt = null;
        return $id;
    }
    
    
    public static function revokeOther($personId, $access)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_other WHERE PersonId=? AND Permission=?");
        
        if (!$stmt->execute(array($personId, $access))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public static function revokeAllOther($personId)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_other WHERE PersonId=?");
        
        if (!$stmt->execute(array($personId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    protected static function existsQuery($sql, $var_array) {
        //Måste märka fältet som räkna med 'Num'
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute($var_array)) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;
            
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        $stmt = null;
        
        
        if ($res[0]['Num'] == 0) return false;
        return true;
    }
    
    
}
    