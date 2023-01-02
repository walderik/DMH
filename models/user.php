<?php

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class User extends BaseModel{
    
    public $Id;
    public $Email;
    public $Password;
    public $IsAdmin = false;
    public $ActivationCode;
    public $EmailChangeCode;
    
    public static $tableName = 'users';
    public static $orderListBy = 'Email';
    
    public static function newFromArray($post){
        $user = static::newWithDefault();
        if (isset($post['Id'])) $user->Id = $post['Id'];
        if (isset($post['Email'])) $user->Email = $post['Email'];
        if (isset($post['Password'])) $user->Password = $post['Password'];
        if (isset($post['IsAdmin'])) $user->IsAdmin = $post['IsAdmin'];
        if (isset($post['ActivationCode'])) $user->ActivationCode = $post['ActivationCode'];
        if (isset($post['EmailChangeCode'])) $user->EmailChangeCode = $post['EmailChangeCode'];
        
        return $user;
    }
     
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing group in db
    public function update()
    {
        global $conn;
        
        $stmt = $conn->prepare("UPDATE ".static::$tableName." SET Email=?, Password=?, IsAdmin=?, ActivationCode=?, EmailChangeCode=? WHERE Id = ?");
        $stmt->bind_param("ssissi", $this->Email, $this->Password, $this->IsAdmin, $this->ActivationCode, $this->EmailChangeCode, $this->Id);

        $stmt->execute();
    }
    
    # Create a new group in db
    public function create()
    {
        global $conn;
        
        $stmt = $conn->prepare("INSERT INTO ".static::$tableName." (Email, Password, IsAdmin, ActivationCode, EmailChangeCode) VALUES (?,?,?,?,?)");
        $stmt->bind_param("siississsiii", $this->Email, $this->Password, $this->IsAdmin, $this->ActivationCode, $this->EmailChangeCode);

        $stmt->execute();
    }
    
    public function getPersons()
    {
        // Get all Person baserat på User_id
    }
}
