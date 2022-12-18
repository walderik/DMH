<?php

class BaseModel {
    
    public static $tableName = 'Set this!';
    public static $orderListBy = 'Set this!';
    
    
    public static function all() {
        global $conn;
        
        $sql = "SELECT * FROM ".static::$tableName." ORDER BY ".static::$orderListBy.";";
        $result = mysqli_query($conn, $sql);
        $resultCheck = mysqli_num_rows($result);
        $telegram_array = array();
        if ($resultCheck > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                $telegram_array[] = static::newFromArray($row);
            }
        }
        return $telegram_array;
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
        $telegram = static::newFromArray($row);
        return $telegram;
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


?>
