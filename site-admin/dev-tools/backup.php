<?php

$root = $_SERVER['DOCUMENT_ROOT'];

include_once($root . '/includes/Mysqldump/Mysqldump.php');

class Backup extends Dbh {
    

    

    public static function doBackup() {
        global $root;
        $tables = Backup::getTableNames();
        
        $backup_file_name = $root . '/tmp/OM_backup_' . time() . '.sql';
        
        //Make backup
        $dumpSettings = array(
            'include-tables' => $tables
        );
        
        $dump = new Ifsnop\Mysqldump\Mysqldump('mysql:host='.Dbh::$dbServername.';dbname='.Dbh::$dbName, Dbh::$dbUsername, Dbh::$dbPassword, $dumpSettings);
        $dump->start($backup_file_name);
        
        //$sqlScript = Backup::makeBackupScript($tables);
        Backup::downloadBackup($backup_file_name);
    }
    
    private static function getTableNames() {
        $sql = "SHOW TABLES";
        
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute()) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $tables = array();
        foreach ($rows as $row) {
            $tablename = $row['Tables_in_berghemsvanner_'];
            if (str_starts_with($tablename, "regsys_")) $tables[] = $tablename;
        }
        
        $stmt = null;
        return $tables;
    }
    
    
    private static function downloadBackup($backup_file_name) {
        // Download the SQL backup file to the browser
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file_name));
        readfile($backup_file_name);
        unlink($backup_file_name);
    }
}