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
    
}


?>
