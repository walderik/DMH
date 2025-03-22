<?php

class SelectionData extends BaseModel{
    
    public  $Id;
    public  $Name;
    public  $Description;
    public  $Active = true;
    public  $SortOrder;
    public  $CampaignId;
    

    public static $orderListBy = 'SortOrder';
    
    # Används den här tabellen
    public static function isInUse(LARP $larp) {
        
        static $in_use;
        //Fungerar inte, verkar hålla en variabel för alla som ärver den.
        //if (!is_null($in_use)) return $in_use;
        
        $stmt = static::connectStatic()->prepare("SELECT * FROM regsys_".strtolower(static::class)." WHERE active = 1 AND CampaignId = ? ORDER BY SortOrder LIMIT 1;");
        
        if (!$stmt->execute(array($larp->CampaignId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $in_use = false;
        } else {
            $in_use = true;
        }
        $stmt = null;
        return $in_use;
    }
    
    public static function loadById($id) {
        if (is_null($id)) return null;
        static $cache = array();
        $key = strtolower(static::class).$id;
        if (isset($cache[$key])) return $cache[$key];
        $cache[$key] = parent::loadById($id);
        return $cache[$key];
    }
    
    public static function newFromArray($post) {
        $selectionData = static::newWithDefault();
        $selectionData->setValuesByArray($post);
        return $selectionData;
    }
     
    public function setValuesByArray($arr) {
        if (isset($arr['SortOrder'])) $this->SortOrder = $arr['SortOrder'];
        if (isset($arr['Active'])) {
            if ($arr['Active'] == "on" || $arr['Active'] == 1) {
                $this->Active = 1;
            }
            else {
                $this->Active = 0;
            }
        }
        else {
            $this->Active = 0;
        }
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Id'])) {
            $this->Id = $arr['Id'];
        }
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        
        $newOne = new static();
        $newOne->SortOrder = (static::numberOff()+1)*100;
        $newOne->Active = 1;
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    public static function allActive(LARP $larp) {       
        $sql = "SELECT * FROM regsys_".strtolower(static::class)." WHERE active = 1 AND CampaignId=? ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function allForLarp(LARP $larp) {
        $sql = "SELECT * FROM regsys_".strtolower(static::class)." WHERE CampaignId=? ORDER BY SortOrder;";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_".strtolower(static::class)." SET SortOrder=?, Active=?, Description=?, Name=? WHERE id = ?");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
         }      
         $stmt = null;
    }
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_".strtolower(static::class)." (SortOrder, Active, Description, Name, CampaignId) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name, $this->CampaignId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }   
    
    
    # En dropdown där man kan välja den här
    public static function selectionDropdown(LARP $larp, ?bool $multiple=false, ?bool $required=true, $selected=null) {
        $selectionDatas = static::allActive($larp);
        selectionByArray(static::class , $selectionDatas, $multiple, $required, $selected);
    }

    
    
    # Hjälptexter till dropdown som förklarar de olika valen.
    public static function helpBox(LARP $larp) {
        $selectionDatas = static::allActive($larp);
        echo "<div class='tooltip'>\n";
        echo "<table class='helpBox'>\n";
        foreach ($selectionDatas as $selectionData) {
            echo "  <tr><td style='vertical-align:top'>" . $selectionData->Name . "</td><td style='vertical-align:top'>" . $selectionData->Description . "</td></tr>\n";
        }
        echo "</table>\n";
        echo "</div>\n";
    }
    
    
    public static function countByType(LARP $larp) {
        if (is_null($larp)) return Array();
        
        $type = strtolower(static::class)."Id";
        $type = static::class."Id";
        
        $sql = "select count(regsys_registration.Id) AS Num, regsys_".strtolower(static::class). ".Name AS Name FROM ".
            "regsys_registration, regsys_person, regsys_".strtolower(static::class)." WHERE ".
            "larpId=? AND ".
            "PersonId = regsys_person.Id AND ".
            "regsys_".strtolower(static::class).".Id=".$type." GROUP BY ".$type.";";

        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(Array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $rows;
        
    
    }
    
    public static function countByTypeOnRoles(LARP $larp, $mainRole) {
        if (is_null($larp)) return Array();
        
        
        if ($mainRole) {
            $mainStr = "regsys_larp_role.IsMainRole=1 AND ";
        }
        else {
            $mainStr = "regsys_larp_role.IsMainRole=0 AND ";
        }
        $type = strtolower(static::class)."Id";
        $type = static::class."Id";
        
        $sql = "select count(regsys_larp_role.RoleId) AS Num, regsys_".strtolower(static::class). ".Name AS Name FROM ".
            "regsys_larp_role, regsys_role, regsys_".strtolower(static::class)." WHERE ".
            "larpId=? AND ".
            "RoleId = regsys_role.Id AND ".
            "regsys_role.NoIntrigue = 0 AND ".
            $mainStr . 
            "regsys_".strtolower(static::class).".Id=".$type." GROUP BY ".$type.";";
 
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(Array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        return $rows;
        
        
    }
    
    
    
    public static function countByTypeOnGroups(LARP $larp) {
        if (is_null($larp)) return Array();
        

        $type = static::class."Id";
        
        $sql = "select count(regsys_larp_group.GroupId) AS Num, regsys_".strtolower(static::class). ".Name AS Name FROM ".
            "regsys_larp_group, regsys_group, regsys_".strtolower(static::class)." WHERE ".
            "larpId=? AND ".
            "GroupId = regsys_group.Id AND ".
            "regsys_".strtolower(static::class).".Id=".$type." GROUP BY ".$type.";";
            
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute(Array($larp->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            
            if ($stmt->rowCount() == 0) {
                $stmt = null;
                return array();
            }
            
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $rows;
            
            
    }
    
    
    public function mayDelete() {
        return false;
    }
    
}