<?php

class AccessControl {
   
    public static function accessControlCampaign() {
        global $current_user, $current_larp;
        if (empty($current_larp) or (empty($current_user))) {
            header('Location: ../index.php');
            exit;
        }
        //If the user has admin privielieges they may always see
        
        if (isset($_SESSION['admin'])) {
            
            return;
        }
        
        if (static::hasAccessCampaign($current_user->Id, $current_larp->CampaignId)) {
            return;
        }
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
        
        if (isset($_SESSION['admin'])) {
            return;
        }
        
        if (static::hasAccessLarp($current_user, $current_larp)) {
            return;
        }
        //Does not have access send to participant
        header('Location: ../participant/index.php');
        exit;
        
    }
    
    
        
    public static function hasAccessCampaign($userId, $campaignId) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_campaign WHERE UserId =? AND CampaignId=?;";
        return static::existsQuery($sql, array($userId, $campaignId));
    }
    
    public static function hasAccessLarp(User $user, LARP $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_campaign WHERE UserId =? AND CampaignId=?;";
        $hasCampaignAccess = static::existsQuery($sql, array($user->Id, $larp->CampaignId));
        if ($hasCampaignAccess) return true;
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control_larp WHERE UserId =? AND LarpId=?;";
        return static::existsQuery($sql, array($user->Id, $larp->Id));
        
    }
    
    public static function grantCampaign($userId, $campaignId) {    
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
    
 
    public static function grantLarp($userId, $larpId) {
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control_larp (UserId, LarpId) VALUES (?,?)");
        
        if (!$stmt->execute(array($userId, $larpId))) {
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
    
    
    
    
}
    