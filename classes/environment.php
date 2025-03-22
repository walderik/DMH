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
        print_r($ini_array);
        if ($ini_array['test'] == "1") return true;
        return false;
    }
    
}

class DatabaseConnectionData {
    public $ServerName;
    public $UserName;
    public $Password;
    public $DBName;
}