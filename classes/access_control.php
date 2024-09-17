<?php

class AccessControl extends Dbh {
    const ADMIN = 0;
    const BOARD = 1;
    const HOUSES = 2;
    
    const ACCESS_TYPES = [
        AccessControl::ADMIN => "OM Admin",
        AccessControl::BOARD => "Styrelse",
        AccessControl::HOUSES => "Husredigering"
    ];
    
    
   
    public static function accessControlCampaign() {
        global $current_user, $current_larp;
        if (empty($current_larp) or (empty($current_user))) {
            header('Location: ../index.php');
            exit;
        }
        //If the user has admin privielieges they may always see
        if (static::hasAccessOther($current_user->Id, AccessControl::ADMIN)) return;
        
        if (static::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) return;
    
        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
    }

    public static function accessControlLarp() {
        global $current_user, $current_larp;
        if (empty($current_larp) or (empty($current_user))) {
            header('Location: ../index.php');
            exit;
        }
        //If the user has admin privielieges they may always see
        if (static::hasAccessOther($current_user->Id, AccessControl::ADMIN)) return;

        if (static::hasAccessLarp($current_user, $current_larp)) return;

        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
    }
    
    public static function accessControlOther($access) {
        global $current_user, $current_larp;
        if (empty($current_larp) or (empty($current_user))) {
            header('Location: ../index.php');
            exit;
        }
        //If the user has admin privielieges they may always see
        if (static::hasAccessOther($current_user->Id, AccessControl::ADMIN)) return;
        
        if (static::hasAccessOther($current_user->Id, $access)) return;
        
        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
        
    }
    
    public static function isMoreThanParticipant(User $user, LARP $larp) {
        if (static::hasAccessLarp($user, $larp)) return true;
        foreach (AccessControl::ACCESS_TYPES as $key => $value) {
            if (static::hasAccessOther($user->Id, $key)) return true;
        }
        return false;
    }
        
    public static function hasAccessCampaign($userId, $campaignId) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_campaign WHERE UserId =? AND CampaignId=?;";
        return static::existsQuery($sql, array($userId, $campaignId));
    }
    
    public static function hasAccessLarp(User $user, LARP $larp) {
        if (static::hasAccessCampaign($user->Id, $larp->CampaignId)) return true;
        
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_larp WHERE UserId =? AND LarpId=?;";
        return static::existsQuery($sql, array($user->Id, $larp->Id));
        
    }
    
    public static function hasAccessOther($userId, int $access) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_other WHERE UserId =? AND (Permission=? OR Permission=?);";
        return static::existsQuery($sql, array($userId, $access, AccessControl::ADMIN));
        
    }
    
    public static function grantCampaign($userId, $campaignId) { 
        if (AccessControl::hasAccessCampaign($userId, $campaignId)) return;
            
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_campaign (UserId, CampaignId) VALUES (?,?)");
        
        if (!$stmt->execute(array($userId, $campaignId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $id = $connection->lastInsertId();
            $stmt = null;
            return $id;
    }
    
    
    public static function revokeCampaign($userId, $campaignId)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_campaign WHERE UserId=? AND CampaignId=?");
        
        if (!$stmt->execute(array($userId, $campaignId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
 
    public static function grantLarp(User $user, Larp $larp) {
        if (AccessControl::hasAccessLarp($user, $larp)) return;
            
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_larp (UserId, LarpId) VALUES (?,?)");
        
        if (!$stmt->execute(array($user->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $id = $connection->lastInsertId();
        $stmt = null;
        return $id;
    }
    
    
    public static function revokeLarp($userId, $larpId) 
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_larp WHERE UserId=? AND LarpId=?");
        
        if (!$stmt->execute(array($userId, $larpId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
 
    public static function grantOther($userId, $access) {
        $connection = static::connectStatic();
        
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_other (UserId, Permission) VALUES (?,?)");
        
        if (!$stmt->execute(array($userId, $access))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $id = $connection->lastInsertId();
        $stmt = null;
        return $id;
    }
    
    
    public static function revokeOther($userId, $access)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control_other WHERE UserId=? AND Permission=?");
        
        if (!$stmt->execute(array($userId, $access))) {
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
    