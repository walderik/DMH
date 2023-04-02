<?php

class SelectionData extends BaseModel{
    
    public  $Id;
    public  $Name;
    public  $Description;
    public  $Active = true;
    public  $SortOrder;
    public  $CampaignId;
    
    //public static $tableName = 'wealths';
    public static $orderListBy = 'SortOrder';
    
    # Används den här tabellen
    public static function is_in_use() {
        global $tbl_prefix; 
        $stmt = static::connectStatic()->prepare("SELECT * FROM `".$tbl_prefix.strtolower(static::class)."` WHERE active = 1 ORDER BY SortOrder LIMIT 1;");
        
        if (!$stmt->execute()) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return false;
        }
        $stmt = null;
        return true;
    }
    
    public static function newFromArray($post) {
        $selectionData = static::newWithDefault();
        if (isset($post['SortOrder'])) $selectionData->SortOrder = $post['SortOrder'];
        if (isset($post['Active'])) {
            if ($post['Active'] == "on" || $post['Active'] == 1) {
                $selectionData->Active = 1;
            }
            else {
                $selectionData->Active = 0;
            }
        }
        else {
            $selectionData->Active = 0;
        }
        if (isset($post['Description'])) $selectionData->Description = $post['Description'];
        if (isset($post['Name'])) $selectionData->Name = $post['Name'];
        if (isset($post['Id'])) {
            $selectionData->Id = $post['Id'];
        }
        if (isset($post['CampaignId'])) $selectionData->CampaignId = $post['CampaignId'];
        return $selectionData;
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
    
    public static function allActive() {
        global $tbl_prefix;        
        //TODO begränsa till kampanjen
        
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray 
        # strtolower(static::class)
        $stmt = static::connectStatic()->prepare("SELECT * FROM `".$tbl_prefix.strtolower(static::class)."` WHERE active = 1 ORDER BY SortOrder;");
        
        if (!$stmt->execute()) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    
    # Update an existing object in db
    public function update() {
        global $tbl_prefix;
        $stmt = $this->connect()->prepare("UPDATE `".$tbl_prefix.strtolower(static::class)."` SET SortOrder=?, Active=?, Description=?, Name=? WHERE id = ?");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
         }      
         $stmt = null;
    }
    
    # Create a new telegram in db
    public function create() {
        global $tbl_prefix;
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `".$tbl_prefix.strtolower(static::class)."` (SortOrder, Active, Description, Name, CampaignId) VALUES (?, ?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name, $this->CampaignId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }   
    
    
    # En dropdown där man kan välja den här
    public static function selectionDropdown(?bool $multiple=false, ?bool $required=true, $selected=null) {
        $selectionDatas = static::allActive();
        selectionByArray(static::class , $selectionDatas, $multiple, $required, $selected);
    }

    
    
    # Hjälptexter till dropdown som förklarar de olika valen.
    public static function helpBox(?bool $only_active=true) {
        $selectionDatas = ($only_active) ? static::allActive() : static::all();
        echo "<div class='tooltip'>\n";
        echo "<table class='helpBox'>\n";
        foreach ($selectionDatas as $selectionData) {
            echo "  <tr><td style='vertical-align:top'>" . $selectionData->Name . "</td><td style='vertical-align:top'>" . $selectionData->Description . "</td></tr>\n";
        }
        echo "</table>\n";
        echo "</div>\n";
    }
    
    
    public static function countByType(?LARP $larp = NULL) {
        global $tbl_prefix, $current_larp;
        
        if (!isset($larp) || is_null($larp)) $larp = $current_larp;
        
        $type = strtolower(static::class)."Id";
        $sql = "select count(".$tbl_prefix."registration.Id) AS Num, ".$tbl_prefix.strtolower(static::class). ".Name AS Name FROM ".
            $tbl_prefix."registration, ".$tbl_prefix."person, ".$tbl_prefix.strtolower(static::class).
            " WHERE larpId=? AND PersonId = ".
            $tbl_prefix."person.Id AND ".$tbl_prefix.strtolower(static::class).".Id=".$type." GROUP BY ".$type.";";

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

        return $rows;
        
    
    }
    
      
}