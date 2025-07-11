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
        //if (!Environment::isTest()) {
            //session_save_path('/om-sessions/');
            //session_save_path('/om-sessions/');
        //}
        /*
        session_save_path('/om-sessions');
        echo "Save path set to ".session_save_path()."<br>";

        ini_set('session.gc_maxlifetime', 86400);
        echo "1<br>";
        session_set_cookie_params(86400);
        echo "2<br>";
        session_start([
            'cookie_lifetime' => 86400,
        ]);
        echo "Session startad.<br>";
        exit;
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

