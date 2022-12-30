<?php

include_once 'includes/db.inc.php';
include 'models/base_model.php';

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class SelectionData extends BaseModel{
    
    public  $id;
    public  $name;
    public  $description;
    public  $active = true;
    public  $sortOrder;
    
    public static $tableName = 'wealth';
    public static $orderListBy = 'SortOrder';
    
    public static function newFromArray($post){
        $selectionData = static::newWithDefault();
        if (isset($post['SortOrder'])) $selectionData->sortOrder = $post['SortOrder'];
        if (isset($post['Active'])) $selectionData->active = $post['Active'];
        if (isset($post['Description'])) $selectionData->description = $post['Description'];
        if (isset($post['Name'])) $selectionData->name = $post['Name'];
        if (isset($post['Id'])) $selectionData->id = $post['Id'];
        
        return $selectionData;
    }
     
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing telegram in db
    public function update()
    {
        global $conn;
        
        $stmt = $conn->prepare("UPDATE ".static::$tableName." SET SortOrder=?, Active=?, Description=?, Name=? WHERE id = ?");
        $stmt->bind_param("iissi", $sortOrder, $active, $description, $name, $id);
        
        // set parameters and execute
        $id = $this->id;
        $name = $this->name;
        $description = $this->description;
        $active = $this->active;
        $sortOrder = $this->sortOrder;
        $stmt->execute();
    }
    
    # Create a new telegram in db
    public function create()
    {
        global $conn;
        
        $stmt = $conn->prepare("INSERT INTO ".static::$tableName." (SortOrder, Active, Description, Name) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiss",  $sortOrder, $active, $description, $name);
        
        // set parameters and execute
        $name = $this->name;
        $description = $this->description;
        $active = $this->active;
        $sortOrder = $this->sortOrder;
        $stmt->execute();
    }
    
      
}

?>