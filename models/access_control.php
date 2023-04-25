<?php

class AccessControl extends BaseModel{
    

    public static function hasAccess(User $user, Larp $larp) {
        $sql = "SELECT COUNT(*) AS Num FROM regsys_access_control WHERE UserId =? AND (CampaignId=? OR CampaignId IS NULL);";
        return static::existsQuery($sql, array($user->Id, $larp->Id)); 
    }
        
    public static function save(User $user, Campaign $campaign) {
        $error = static::maySave();
        if (isset($error)) return null;
        
        $connection = static::connectStatic();
        $stmt = $connection->prepare("INSERT INTO regsys_access_control (UserId, CampaignId) VALUES (?,?)");
        
        if (!$stmt->execute(array($user->Id, $campaign->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $id = $connection->lastInsertId();
            $stmt = null;
            return $id;
    }
    
    
    # Create a new image in db
    public function delete($id) {
        $connection = $this->connect();
        $stmt = $connection->prepare("DELETE FROM regsys_access_control WHERE Id=?");
        
        if (!$stmt->execute(array($id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
        return;
    }
    
    
    
    
    
}
    