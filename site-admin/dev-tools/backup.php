<?php

class Backup extends Dbh {

    public static function doBackup() {
        $tables = Backup::getTableNames();
        $sqlScript = Backup::makeBackupScript($tables);
        Backup::downloadScript($sqlScript);
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
    
    private static function makeBackupScript($tables) {
        $sqlScript = "";
        foreach ($tables as $table) {
            
            // Prepare SQLscript for creating table structure
            $sql = "SHOW CREATE TABLE $table";
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute()) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            
            $rows = $stmt->fetchAll(PDO::FETCH_NUM);
            
            $sqlScript .= "\n\n" . $rows[0][1] . ";\n\n";
            
            $sql = "SELECT * FROM $table";
            $stmt = static::connectStatic()->prepare($sql);
            
            if (!$stmt->execute()) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            
            $rows = $stmt->fetchAll(PDO::FETCH_NUM);
            
            
            foreach($rows as $row) {
                $sqlScript .= "INSERT INTO $table VALUES(";
                $columnCount = count($row);
                for ($j = 0; $j < $columnCount; $j ++) {
                    $row[$j] = $row[$j];
                    
                    if (isset($row[$j])) {
                        $sqlScript .= '"' . $row[$j] . '"';
                    } else {
                        $sqlScript .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $sqlScript .= ',';
                    }
                }
                $sqlScript .= ");\n";
                
            }
            
        }
        return $sqlScript;
        
    }
    
    
    private static function downloadScript($sqlScript) {
        global $root;
        if(!empty($sqlScript)) {
            $sqlScript = "SET FOREIGN_KEY_CHECKS=0;\n\n" . $sqlScript . "\n\nSET FOREIGN_KEY_CHECKS=1;";
            // Save the SQL script to a backup file
            $backup_file_name = $root . '/tmp/OM_backup_' . time() . '.sql';
            $fileHandler = fopen($backup_file_name, 'w+');
            fwrite($fileHandler, $sqlScript);
            fclose($fileHandler);
            
            // Download the SQL backup file to the browser
            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($backup_file_name));
            //ob_clean();
            flush();
            readfile($backup_file_name);
            unlink($backup_file_name);
            //exec('rm ' . $backup_file_name);
        }
        
    }
}