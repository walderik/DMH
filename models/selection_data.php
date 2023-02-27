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
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray 
        # strtolower(static::class)
        $stmt = static::connectStatic()->prepare("SELECT * FROM `".strtolower(static::class)."` WHERE active = 1 ORDER BY SortOrder;");
        
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
        $stmt = $this->connect()->prepare("UPDATE `".strtolower(static::class)."` SET SortOrder=?, Active=?, Description=?, Name=? WHERE id = ?");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
         }      
         $stmt = null;
    }
    
    # Create a new telegram in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `".strtolower(static::class)."` (SortOrder, Active, Description, Name, CampaignId) VALUES (?, ?, ?, ?, ?)");
        
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
        selectionDropdownByArray(static::class , $selectionDatas, $multiple, $required, $selected);
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
      
}