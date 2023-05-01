<?php

class AccessControl extends BaseModel{
    public $Id;
    public $UserId;
    public $CampaignId;
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    
    public static function newFromArray($post){
        $ac = static::newWithDefault();
        $ac->setValuesByArray($post);
        return $ac;
    }
    
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['UserId'])) $this->UserId = $arr['UserId'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        
    }
    
   
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


        
    public static function hasAccessCampaign($userId, $campaignId) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control WHERE UserId =? AND CampaignId=?;";
        return static::existsQuery($sql, array($userId, $campaignId));
    }
    
    public static function loadByIds($userId, $campaignId) {
        if (!isset($userId) or !isset($campaignId)) return null;
        
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $sql = "SELECT * FROM regsys_access_control WHERE UserId = ? AND CampaignId = ?";
        return static::getOneObjectQuery($sql, array($userId, $campaignId));
        
    }
    
    public static function save($userId, $campaignId) {    
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control (UserId, CampaignId) VALUES (?,?)");
        
        if (!$stmt->execute(array($userId, $campaignId))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $id = $connection->lastInsertId();
            $stmt = null;
            return $id;
    }
    
    
    public static function delete($id)
    {
        $stmt = static::connectStatic()->prepare("DELETE FROM regsys_access_control WHERE Id = ?");
        
        if (!$stmt->execute(array($id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    
    
    
}
    