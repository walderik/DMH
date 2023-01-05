<?php

class SelectionData extends BaseModel{
    
    public  $Id;
    public  $Name;
    public  $Description;
    public  $Active = true;
    public  $SortOrder;
    
    //public static $tableName = 'wealths';
    public static $orderListBy = 'SortOrder';
    
    public static function newFromArray($post){
        $selectionData = static::newWithDefault();
        if (isset($post['SortOrder'])) $selectionData->SortOrder = $post['SortOrder'];
        if (isset($post['Active'])) {
            if ($post['Active'] == "on" or $post['Active'] == 1) {
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
        if (isset($post['Id'])) $selectionData->Id = $post['Id'];
        
        return $selectionData;
    }
     
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    public static function allActive() {
        # Gör en SQL där man söker baserat på ID och returnerar ett object mha newFromArray
        $stmt = static::connectStatic()->prepare("SELECT * FROM ".static::$tableName." WHERE active = 1 ORDER BY SortOrder;");
        
        if (!$stmt->execute()) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {

            $testrad = static::newWithDefault();
            $testrad->Id = 1;
            $testrad->Name = 'Inget data finns ännu i:';
            $testrad->Description = 'Notering för admins';
            $resultArray[] = $testrad;
            
            $testrad = static::newWithDefault();
            $testrad->Id = 19;
            $testrad->Name = static::$tableName;
            $testrad->Description = 'Tabellen som är tom';
            
            $resultArray[] = $testrad;
            $testrad = static::newWithDefault();
            $testrad->Id = 29;
            $testrad->Name = static::class;
            $testrad->Description = 'Klassen som saknar object';
            
            $resultArray[] = $testrad;

        }
        else {
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $resultArray = array();
            foreach ($rows as $row) {
                $resultArray[] = static::newFromArray($row);
            }
        }
        $stmt = null;
        return $resultArray;
    }
    
    # Update an existing object in db
    public function update()
    {
        $stmt = $this->connect()->prepare("UPDATE ".static::$tableName." SET SortOrder=?, Active=?, Description=?, Name=? WHERE id = ?");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
         }
            
         $stmt = null;
    }
    
    # Create a new telegram in db
    public function create()
    {
        
        $stmt = $this->connect()->prepare("INSERT INTO ".static::$tableName." (SortOrder, Active, Description, Name) VALUES (?, ?, ?, ?)");
        
        if (!$stmt->execute(array($this->SortOrder, $this->Active, $this->Description, $this->Name))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
        }
            
        $stmt = null;
    }
    
    
    # En dropdown där man kan välja den här
    public static function selectionDropdown(?bool $multiple=false, ?bool $required=true, ?bool $only_active=true){
        $selectionDatas = ($only_active) ? static::allActive() : static::all();
//         $name   = ($multiple) ? (static::$tableName . "[]") : static::class ;
        $name   = ($multiple) ? (strtolower(static::class) . "[]") : strtolower(static::class) ;
        $option = ($multiple) ? ' multiple' : '';
        $option = ($required) ? $option . ' required' : $option;
        $size   = count($selectionDatas);
        
        echo "<div class='selectionDropdown'>\n";
        echo "<select name='" . $name . "' id='" . static::$tableName . "' size=".$size." " . $option . ">\n";
        foreach ($selectionDatas as $selectionData) {
            echo "  <option value='" . $selectionData->Id . "' title='" . $selectionData->Description . "'>" . $selectionData->Name . "</option>\n";
        }
        echo "</select>\n";
        echo "</div>\n";
    }
    
    # Hjälptexter till dropdown som förklarar de olika valen.
    public static function helpBox(?bool $only_active=true){
        $selectionDatas = ($only_active) ? static::allActive() : static::all();
        echo "<div class='tooltip'>\n";
        echo "<table class='helpBox'>\n";
        foreach ($selectionDatas as $selectionData) {
            echo "  <tr><td>" . $selectionData->Name . "</td><td>" . $selectionData->Description . "</td></tr>\n";
        }
        echo "</table>\n";
        echo "</div>\n";
    }
      
}