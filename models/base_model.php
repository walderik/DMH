<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class BaseModel {
    
    public static $tableName = 'Set this!';
    public static $orderListBy = 'Set this!';
    
    
    public static function all() {
        global $conn;
        
        $sql = "SELECT * FROM ".static::$tableName." ORDER BY ".static::$orderListBy.";";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        $resultArray = array();
        if ($resultCheck > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
//                 print_r($row);
                $resultArray[] = static::newFromArray($row);
            }
        }
        return $resultArray;
    }
    
    public static function loadById($id)
    {
        # Gör en SQL där man söker baserat på ID och returnerar ett Telegram-object mha newFromArray
        global $conn;
        
        $stmt = $conn->prepare("SELECT * FROM ".static::$tableName." WHERE Id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result(); // get the mysqli result
        $row = $result->fetch_assoc(); // fetch data
//         $telegram = static::newFromArray($row);
        return static::newFromArray($row);
    }
    
    # Normalt bör man inte anropa den här direkt utan newWithDefault
    public function __construct() {}
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $object = new self();
        return $object;
    }
    
    public static function delete($id)
    {
        global $conn;
        
        $stmt = $conn->prepare("DELETE FROM ".static::$tableName." WHERE Id = ?");
        $stmt->bind_param("i", $id);
        
        // set parameters and execute
        $stmt->execute();
    }
    
    public function destroy()
    {
        static::delete($this->id);
    }
    
}