<?php

class RoleApprovedCopy extends BaseModel {
    public $Id;
    public $RoleId;
    
    public $Name;
    public $Profession;
    public $Description;
    public $DescriptionForGroup;
    public $DescriptionForOthers;
    public $PreviousLarps;
    public $ReasonForBeingInSlowRiver;
    public $ReligionId;
    public $Religion;
    public $BeliefId;
    public $DarkSecret;
    public $DarkSecretIntrigueIdeas;
    public $IntrigueSuggestions;
    public $NotAcceptableIntrigues;
    public $OtherInformation;
    public $PersonId;
    public $GroupId;
    public $WealthId;
    public $PlaceOfResidenceId;
    public $RaceId;
    public $CouncilId;
    public $Council;
    public $GuardId;
    public $RoleFunctionComment;
    public $Photo;
    public $Birthplace;
    public $CharactersWithRelations;
    public $CampaignId;
    public $ImageId;
    public $IsDead = 0;
    public $OrganizerNotes;
    public $NoIntrigue = 0; //"Myslajvare"
    public $LarperTypeId;
    public $TypeOfLarperComment;
    public $RaceComment;
    public $AbilityComment;
    public $IsApproved = 0;
    public $ApprovedByUserId;
    public $ApprovedDate;
    
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $role = static::newWithDefault();
        $role->setValuesByArray($post);
        return $role;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        
        if (isset($arr['Id']))   $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Profession'])) $this->Profession = $arr['Profession'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['DescriptionForGroup'])) $this->DescriptionForGroup = $arr['DescriptionForGroup'];
        if (isset($arr['DescriptionForOthers'])) $this->DescriptionForOthers = $arr['DescriptionForOthers'];
        if (isset($arr['PreviousLarps'])) $this->PreviousLarps = $arr['PreviousLarps'];
        if (isset($arr['ReasonForBeingInSlowRiver'])) $this->ReasonForBeingInSlowRiver = $arr['ReasonForBeingInSlowRiver'];
        if (isset($arr['ReligionId'])) $this->ReligionId = $arr['ReligionId'];
        if (isset($arr['Religion'])) $this->Religion = $arr['Religion'];
        if (isset($arr['BeliefId'])) $this->BeliefId = $arr['BeliefId'];
        if (isset($arr['DarkSecret'])) $this->DarkSecret = $arr['DarkSecret'];
        if (isset($arr['DarkSecretIntrigueIdeas'])) $this->DarkSecretIntrigueIdeas = $arr['DarkSecretIntrigueIdeas'];
        if (isset($arr['IntrigueSuggestions'])) $this->IntrigueSuggestions = $arr['IntrigueSuggestions'];
        if (isset($arr['NotAcceptableIntrigues'])) $this->NotAcceptableIntrigues = $arr['NotAcceptableIntrigues'];
        if (isset($arr['OtherInformation'])) $this->OtherInformation = $arr['OtherInformation'];
        if (isset($arr['PersonId'])) $this->PersonId = $arr['PersonId'];
        if (isset($arr['GroupId'])) $this->GroupId = $arr['GroupId'];
        if (isset($arr['WealthId'])) $this->WealthId = $arr['WealthId'];
        if (isset($arr['PlaceOfResidenceId'])) $this->PlaceOfResidenceId = $arr['PlaceOfResidenceId'];
        if (isset($arr['RaceId'])) $this->RaceId = $arr['RaceId'];
        if (isset($arr['CouncilId'])) $this->CouncilId = $arr['CouncilId'];
        if (isset($arr['Council'])) $this->Council = $arr['Council'];
        if (isset($arr['GuardId'])) $this->GuardId = $arr['GuardId'];
        if (isset($arr['RoleFunctionComment'])) $this->RoleFunctionComment = $arr['RoleFunctionComment'];
        if (isset($arr['Birthplace'])) $this->Birthplace = $arr['Birthplace'];
        if (isset($arr['CharactersWithRelations'])) $this->CharactersWithRelations = $arr['CharactersWithRelations'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['IsDead'])) $this->IsDead = $arr['IsDead'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['NoIntrigue'])) $this->NoIntrigue = $arr['NoIntrigue'];
        if (isset($arr['LarperTypeId'])) $this->LarperTypeId = $arr['LarperTypeId'];
        if (isset($arr['TypeOfLarperComment'])) $this->TypeOfLarperComment = $arr['TypeOfLarperComment'];
        if (isset($arr['RaceComment'])) $this->RaceComment = $arr['RaceComment'];
        if (isset($arr['AbilityComment'])) $this->AbilityComment = $arr['AbilityComment'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        if (isset($arr['ApprovedByUserId'])) $this->ApprovedByUserId = $arr['ApprovedByUserId'];
        if (isset($arr['ApprovedDate'])) $this->ApprovedDate = $arr['ApprovedDate'];
        
        if (isset($this->LarperTypeId) && $this->LarperTypeId=='null') $this->LarperTypeId = null;
        if (isset($this->PlaceOfResidenceId) && $this->PlaceOfResidenceId=='null') $this->PlaceOfResidenceId = null;
        if (isset($this->RaceId) && $this->RaceId=='null') $this->RaceId = null;
        if (isset($this->GuardId) && $this->GuardId=='null') $this->GuardId = null;
        if (isset($this->CouncilId) && $this->CouncilId=='null') $this->CouncilId = null;
        if (isset($this->WealthId) && $this->WealthId=='null') $this->WealthId = null;
        if (isset($this->GroupId) && $this->GroupId=='null') $this->GroupId = null;
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        $newOne = new self();
        return $newOne;
    }
    
       
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_roleapprovedcopy (RoleId, Name, Profession, Description,
                                                            DescriptionForGroup, DescriptionForOthers, PreviousLarps,
                                                            ReasonForBeingInSlowRiver, ReligionId, Religion, BeliefId, DarkSecret, DarkSecretIntrigueIdeas,
                                                            IntrigueSuggestions, NotAcceptableIntrigues, OtherInformation,
                                                            GroupId, WealthId, PlaceOfResidenceId, RaceId, CouncilId, Council, GuardId,
                                                            RoleFunctionComment, Birthplace, CharactersWithRelations,
                                    NoIntrigue, LarperTypeId, TypeOfLarperComment, RaceComment, AbilityComment, ApprovedByUserId, ApprovedDate)
                                    VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?);");
        
        if (!$stmt->execute(array($this->RoleId, $this->Name, $this->Profession, $this->Description,
            $this->DescriptionForGroup, $this->DescriptionForOthers,$this->PreviousLarps,
            $this->ReasonForBeingInSlowRiver, $this->ReligionId, $this->Religion, $this->BeliefId, $this->DarkSecret, $this->DarkSecretIntrigueIdeas,
            $this->IntrigueSuggestions, $this->NotAcceptableIntrigues, $this->OtherInformation, 
            $this->GroupId, $this->WealthId, $this->PlaceOfResidenceId, $this->RaceId,
            $this->CouncilId, $this->Council, $this->GuardId, $this->RoleFunctionComment, $this->Birthplace, $this->CharactersWithRelations,
            $this->NoIntrigue, $this->LarperTypeId, $this->TypeOfLarperComment,
            $this->RaceComment, $this->AbilityComment, $this->ApprovedByUserId, $this->ApprovedDate
        ))) {
            $this->connect()->rollBack();
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    # Hämta intrigtyperna
    public function getIntrigueTypes(){
        return IntrigueType::getIntrigeTypesForApprovedRoleCopy($this->Id);
    }
    
    
    public function saveAllIntrigueTypes($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_intriguetype_role_approved_copy (IntrigueTypeId, RoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIntrigueTypes() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_intriguetype_role_approved_copy WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    # Hämta intrigtyperna
    public function getAbilities(){
        return Ability::getAbilitiesForApprovedRoleCopy($this->Id);
    }
     
    public function saveAllAbilities($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_ability_role_approved_copy (AbilityId, RoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllAbilities() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_ability_role_approved_copy WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public function getRoleFunctions(){
        return RoleFunction::getRoleFunctionsForApprovedRoleCopy($this->Id);
    }
 
    public function saveAllRoleFunctions($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_rolefunction_role_approved_copy (RoleFunctionId, RoleId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllRoleFunctions() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_rolefunction_role_approved_copy WHERE RoleId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function isMysLajvare() {
        if ($this->NoIntrigue == 1) return true;
        return false;
        
    }
    
    public function getGroup() {
        return Group::loadById($this->GroupId);
    }
    
    public function getReligion() {
        if (is_null($this->ReligionId)) return null;
        return Religion::loadById($this->ReligionId);
    }
    
    public function getBelief() {
        if (is_null($this->BeliefId)) return null;
        return Belief::loadById($this->BeliefId);
    }
    
    public function getWealth() {
        if (is_null($this->WealthId)) return null;
        return Wealth::loadById($this->WealthId);
    }
    
    public function getPlaceOfResidence() {
        if (is_null($this->PlaceOfResidenceId)) return null;
        return PlaceOfResidence::loadById($this->PlaceOfResidenceId);
    }
    
    public function getRace() {
        if (is_null($this->RaceId)) return null;
        return Race::loadById($this->RaceId);
    }
    
    public function getCouncil() {
        if (is_null($this->CouncilId)) return null;
        return Council::loadById($this->CouncilId);
    }
    public function getGuard() {
        if (is_null($this->GuardId)) return null;
        return Guard::loadById($this->GuardId);
    }
    
    
    
    public static function makeCopyOfApprovedRole(Role $role) {
        $sql = "SELECT * FROM regsys_role WHERE Id = ?";
        $roleCopy =  RoleApprovedCopy::getOneObjectQuery($sql, array($role->Id));
        $roleCopy->RoleId = $role->Id;
        $roleCopy->create();
        
        $roleCopy->saveAllAbilities($role->getSelectedAbilityIds());
        $roleCopy->saveAllIntrigueTypes($role->getSelectedIntrigueTypeIds());
        $roleCopy->saveAllRoleFunctions($role->getSelectedRoleFunctionIds());
        
    }
    
    public static function getOldRole($roleId) {
        $sql = "SELECT * FROM regsys_roleapprovedcopy WHERE RoleId =?";
        return static::getOneObjectQuery($sql, array($roleId));
    }
    
    public static function delete($id) {
        $roleCopy = RoleApprovedCopy::loadById($id);
        $roleCopy->deleteAllAbilities();
        $roleCopy->deleteAllIntrigueTypes();
        $roleCopy->deleteAllRoleFunctions();
        parent::delete($id);
    }
    
}