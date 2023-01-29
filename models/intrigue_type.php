<?php

class IntrigueType extends SelectionData{
      

    public static function getIntrigeTypesForLarpAndRole($larpId, $roleId) {
        if (is_null($larpId) || is_null($roleId)) return array();
        $sql = "SELECT * from `intriguetype` WHERE Id in (SELECT * FROM `intriguetype_larp_role` WHERE LARP_RoleLARPid = ? AND LARP_RoleRoleId = ?);";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larpId, $roleId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = IntrigueType::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }

}