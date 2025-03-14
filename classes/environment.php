<?php

class Environment {
    
    public static function getHost() {
        return (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]";
    }
    
    public static function getDbConnectionData() {
        $ini_array = parse_ini_file("../.htsettings.ini");
        
        $connectionData = new DatabaseConnectionData();
        $connectionData->ServerName = $ini_array['Servername'];
        $connectionData->UserName = $ini_array['Username'];
        $connectionData->Password = $ini_array['Password'];
        $connectionData->DBName = $ini_array['DBName'];
        return $connectionData;
    }
    
    public static function isTest() {
        $ini_array = parse_ini_file("../.htsettings.ini");
        if (self::$ini_array['test'] == "false") return false;
        return true;
    }
    
}

class DatabaseConnectionData {
    public $ServerName;
    public $UserName;
    public $Password;
    public $DBName;
}