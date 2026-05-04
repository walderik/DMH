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
    
    public function saveAllPermissions($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_officialtype_permission (OfficialTypeId, Permission) VALUES (?,?);");
            if (!$stmt->execute(array($this->Id, $Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    
    
    public function deleteAllPermissions() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_officialtype_permission WHERE OfficialTypeId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getPermissions() {
        $sql = "SELECT Permission FROM regsys_officialtype_permission WHERE OfficialTypeId = ? ORDER BY Permission;";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $stmt = null;
        
        $resPermissions = array();
        foreach ($res as $item)  $resPermissions[] = $item['Permission'];
        return $resPermissions;
    }
    
}