<?php

class Environment {
    
    public static function getHost() {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
    }
    
    public static function getDbConnectionData() {
        global $root;
        $ini_array = parse_ini_file($root . "/.htsettings.ini");
        
        $connectionData = new DatabaseConnectionData();
        $connectionData->ServerName = $ini_array['Servername'];
        $connectionData->UserName = $ini_array['Username'];
        $connectionData->Password = $ini_array['Password'];
        $connectionData->DBName = $ini_array['DBName'];
        return $connectionData;
    }
    
    public static function isTest() {
        global $root;
        $ini_array = parse_ini_file($root . "/.htsettings.ini");
        if ($ini_array['test'] == "1") return true;
        return false;
    }
    
    public static function startSession() {
/*       if (!isset($_SESSION['CREATED'])) {
            $_SESSION['CREATED'] = time();
        } else if (time() - $_SESSION['CREATED'] > 1800) {
            // session started more than 30 minutes ago
            session_regenerate_id(true);    // change session ID for the current session and invalidate old session ID
            $_SESSION['CREATED'] = time();  // update creation time
        }
*/
        session_start();
    }
    
}

class DatabaseConnectionData {
    public $ServerName;
    public $UserName;
    public $Password;
    public $DBName;
}

