<?php

class Role extends BaseModel{
    
    public $Id;
    public $Name;
    public $IsNPC = false;
    public $Profession;
    public $Description;
    public $PreviousLarps;
    public $ReasonForBeingInSlowRiver;
    public $Religion;
    public $DarkSecret;
    public $DarkSecretIntrigueIdeas;
    public $IntrigueSuggestions;
    public $NotAcceptableIntrigues;
    public $OtherInformation; 
    public $PersonId;
    public $GroupId;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $Photo;
    public $Birthplace;
    public $CharactersWithRelations;
    public $CampaignId;
    

    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        if (isset($post['Id']))   $role->Id = $post['Id'];
        if (isset($post['Name'])) $role->Name = $post['Name'];
        if (isset($post['IsNPC'])) $role->IsNPC = $post['IsNPC'];
        if (isset($post['Profession'])) $role->Profession = $post['Profession'];
        if (isset($post['Description'])) $role->Description = $post['Description'];
        if (isset($post['PreviousLarps'])) $role->PreviousLarps = $post['PreviousLarps'];
        if (isset($post['ReasonForBeingInSlowRiver'])) $role->ReasonForBeingInSlowRiver = $post['ReasonForBeingInSlowRiver'];
        if (isset($post['Religion'])) $role->Religion = $post['Religion'];
        if (isset($post['DarkSecret'])) $role->DarkSecret = $post['DarkSecret'];
        if (isset($post['DarkSecretIntrigueIdeas'])) $role->DarkSecretIntrigueIdeas = $post['DarkSecretIntrigueIdeas'];
        if (isset($post['IntrigueSuggestions'])) $role->IntrigueSuggestions = $post['IntrigueSuggestions'];
        if (isset($post['NotAcceptableIntrigues'])) $role->NotAcceptableIntrigues = $post['NotAcceptableIntrigues'];
        if (isset($post['OtherInformation'])) $role->OtherInformation = $post['OtherInformation'];
        if (isset($post['PersonId'])) $role->PersonId = $post['PersonId'];
        if (isset($post['GroupId'])) $role->GroupId = $post['GroupId'];
        if (isset($post['WealthId'])) $role->WealthId = $post['WealthId'];
        if (isset($post['PlaceOfResidenceId'])) $role->PlaceOfResidenceId = $post['PlaceOfResidenceId'];
        if (isset($post['Photo'])) $role->Photo = $post['Photo'];
        if (isset($post['Birthplace'])) $role->Birthplace = $post['Birthplace'];
        if (isset($post['CharactersWithRelations'])) $role->CharactersWithRelations = $post['CharactersWithRelations'];
        if (isset($post['CampaignId'])) $role->CampaignId = $post['CampaignId'];
        
        if (isset($role->GroupId) && $role->GroupId=='null') $role->GroupId = null;
        
        return $role;
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $newOne = new self();
        $newOne->CampaignId = $current_larp->CampaignId;
        return $newOne;
    }
    
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE `role` SET Name=?, IsNPC=?, Profession=?, Description=?,
                                                              PreviousLarps=?, ReasonForBeingInSlowRiver=?, Religion=?, DarkSecret=?,
                                                              DarkSecretIntrigueIdeas=?, IntrigueSuggestions=?, NotAcceptableIntrigues=?, OtherInformation=?,
                                                              PersonId=?, GroupId=?, WealthId=?, PlaceOfResidenceId=?, Photo=?, Birthplace=?, 
                                                              CharactersWithRelations=?, CampaignId=? WHERE Id = ?;");
        
        if (!$stmt->execute(array($this->Name, $this->IsNPC, $this->Profession, $this->Description, $this->PreviousLarps,
            $this->ReasonForBeingInSlowRiver, $this->Religion, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, $this->PersonId, 
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->Photo,
            $this->Birthplace, $this->CharactersWithRelations, $this->CampaignId, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    # Create a new object in db
    public function create() {       
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO `role` (Name, IsNPC, Profession, Description, PreviousLarps,
                                                            ReasonForBeingInSlowRiver, Religion, DarkSecret, DarkSecretIntrigueIdeas,
                                                            IntrigueSuggestions, NotAcceptableIntrigues, OtherInformation, PersonId,
                                                            GroupId, WealthId, PlaceOfResidenceId, Photo,
                                                            Birthplace, CharactersWithRelations, CampaignId) VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->IsNPC, $this->Profession, $this->Description, $this->PreviousLarps,
            $this->ReasonForBeingInSlowRiver, $this->Religion, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, $this->PersonId,
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->Photo,
            $this->Birthplace, $this->CharactersWithRelations, $this->CampaignId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    
    public function getGroup() {
        return Group::loadById($this->GroupId);
    }
    
    public function getPerson() {
        return Person::loadById($this->PersonId);
    }

    public function isRegistered(LARP $larp) {
        return LARP_Role::isRegistered($this->Id, $larp->Id);
        
    }    
    
    public function isMain(LARP $larp) {
        $larp_role = LARP_Role::loadByIds($this->Id, $larp->Id);
        return $larp_role->IsMainRole;
    }
    
    
    public static function getRolesForPerson($personId) {
        if (is_null($personId)) return Array();
        $sql = "SELECT * FROM `role` WHERE PersonId = ? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($personId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    
    # Hämta de roller en person har anmält till ett lajv
    public static function getRegistredRolesForPerson(Person $person, LARP $larp) {
        if (is_null($person) || is_null($larp)) return Array();
        $sql = "SELECT * FROM `role`, larp_role WHERE `role`.PersonId = ? AND `role`.Id=larp_role.RoleId AND larp_role.LarpId=? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($group->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
   
    # Hämta anmälda deltagare i en grupp
    public static function getRegisteredRolesInGroup($group, $larp) {
        if (is_null($group) || is_null($larp)) return Array();
        $sql = "SELECT * FROM `role`, larp_role WHERE `role`.GroupId = ? AND `role`.Id=larp_role.RoleId AND larp_role.LarpId=? ORDER BY ".static::$orderListBy.";";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($group->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }

        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = static::newFromArray($row);
        }
        $stmt = null;
        return $resultArray;
    }
    

}