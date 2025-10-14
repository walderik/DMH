<?php

class Subdivision extends BaseModel{
    
    public $Id;
    public $Name;
    public $Description;
    public $OrganizerNotes;
    public $CampaignId;
    public $IsVisibleToParticipants = 0;
    public $CanSeeOtherParticipants = 0;
    public $Rule = "";
    
    public static $orderListBy = 'Name';
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['IsVisibleToParticipants'])) $this->IsVisibleToParticipants = $arr['IsVisibleToParticipants'];
        if (isset($arr['CanSeeOtherParticipants'])) $this->CanSeeOtherParticipants = $arr['CanSeeOtherParticipants'];
        if (isset($arr['Rule'])) $this->Rule = $arr['Rule'];
    }
    
        
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $object = new self();
        $object->CampaignId = $current_larp->CampaignId;
        return $object;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_subdivision SET Name=?, Description=?, OrganizerNotes=?, IsVisibleToParticipants=?, CanSeeOtherParticipants=?, Rule=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->IsVisibleToParticipants, $this->CanSeeOtherParticipants, $this->Rule, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_subdivision (Name, Description, OrganizerNotes, IsVisibleToParticipants, CanSeeOtherParticipants, Rule, CampaignId) VALUES (?,?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->OrganizerNotes, $this->IsVisibleToParticipants, $this->CanSeeOtherParticipants, $this->Rule, $this->CampaignId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    public function hasImage() {
        return false;
    }
    
    public function isVisibleToParticipants() {
        if ($this->IsVisibleToParticipants==1) return true;
        return false;
    }
    
    public function canSeeOtherParticipants() {
        if ($this->CanSeeOtherParticipants==1) return true;
        return false;
    }
    
    public function getViewLink() {
        return "<a href='view_subdivision.php?id=$this->Id'>$this->Name</a>";
    }
    
    public function getEditLinkPen() {
        return "<a href='subdivision_form.php?operation=update&id=$this->Id'><i class='fa-solid fa-pen'></i></a>";
    }
    
    public function clearRule() {
        $this->Rule = "";
        $this->update();
    }
    
    public function addRule($key, $values) {
        $rule = json_decode($this->Rule, true);
        if (empty($rule)) $rule = array();
        $rule[$key] = $values;
        $this->Rule = json_encode($rule);
        $this->update();
    }
    
    public function getRuleTextArray() {
        global $current_larp;
        if (empty($this->Rule)) return array();
        $rule = json_decode($this->Rule, true);
        
        $options = getAllOptionsForRoles($current_larp);
        $types = getAllTypesForRoles($current_larp);
        
        $textarray = array();
        foreach ($types as $key => $type) {
            if (array_key_exists ($key, $rule)) {
                $rulevalue = $rule[$key];
                $values = $options[$key];
                $chosenAlternativesText = array();
                foreach ($values as $val) {
                    if (in_array($val->Id, $rulevalue)) {
                        $chosenAlternativesText[] = $val->Name;
                    }
                }
                $textarray[] = $type .  " = " . implode(", ", $chosenAlternativesText);
            }
        }
        return $textarray;
        //return ["Religion = Ateist, Agnostiker", "Var karaktärer bor = Slow River"];
    }
    
    public function getRuleSelectedValues($key) {
        if (empty($this->Rule)) return array();
        $rule = json_decode($this->Rule, true);
        if (array_key_exists ($key, $rule)) return $rule[$key];
        else return array();
     }
    
    public function addMembers($roleIds) {
        //Ta reda på vilka som inte redan är kopplade till grupperingen
        $exisitingIds = array();
        $members = $this->getAllManualMembers();
        foreach ($members as $member) {
            $exisitingIds[] = $member->Id;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingIds);
        //Koppla magier till skolan
        foreach ($newRoleIds as $roleId) {
            $this->addMember($roleId);
        }
    }
    
    private function addMember($roleId) {
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_subdivisionmember (SubdivisionId, RoleId) VALUES (?,?);");
        if (!$stmt->execute(array($this->Id, $roleId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    public function removeMember($roleId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_subdivisionmember WHERE SubdivisionId=? AND RoleId = ?;");
        if (!$stmt->execute(array($this->Id, $roleId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
     public function getAllRegisteredMembers(LARP $larp) {
        return array_merge($this->getAllAutomaticRegisteredMembers($larp), $this->getAllManualRegisteredMembers($larp));
    }
    
    public function getAllAutomaticRegisteredMembers(LARP $larp) {
        if (empty($this->Rule)) return array();
        $rule = json_decode($this->Rule, true);
        $first = true;
        foreach($rule as $key => $rulepart) {
            if ($first) $memberRoles = Role::getAllWithTypeValues($larp->Id, $key, $rulepart);
            else $memberRoles = array_uintersect($memberRoles, Role::getAllWithTypeValues($larp->Id, $key, $rulepart),
                function ($objOne, $objTwo) {
                    return $objOne->Id - $objTwo->Id;
                });
            $first = false;
        }

        return $memberRoles;
    }
    public function getAllManualMembers() {
        $sql = "SELECT regsys_role.* FROM regsys_role WHERE Id IN (".
            "SELECT RoleId FROM regsys_subdivisionmember WHERE ".
            " SubdivisionId=?) ".
            "ORDER BY ".Role::$orderListBy.";";
        
        return Role::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    
    public function getAllManualRegisteredMembers(LARP $larp) {
        $sql = "SELECT regsys_role.* FROM regsys_role, regsys_larp_role WHERE Id IN (".
            "SELECT RoleId FROM regsys_subdivisionmember, regsys_registration WHERE ".
            " SubdivisionId=? AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.LARPId = ? AND ".
            "regsys_registration.NotComing = 0) AND ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = ? ".
            "ORDER BY ".Role::$orderListBy.";";
        
        return Role::getSeveralObjectsqQuery($sql, array($this->Id, $larp->Id, $larp->Id));
    }

    
    public function getAllManualMembersNotComing(LARP $larp) {
        $sql = "SELECT regsys_role.* FROM regsys_role, regsys_larp_role WHERE Id IN (".
            "SELECT RoleId FROM regsys_subdivisionmember, regsys_registration WHERE ".
            " SubdivisionId=? AND ".
            "regsys_role.PersonId = regsys_registration.PersonId AND ".
            "regsys_registration.LARPId = ? AND ".
            "regsys_registration.NotComing = 1) AND ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LarpId = ? ".
            "ORDER BY ".Role::$orderListBy.";";
        
        return Role::getSeveralObjectsqQuery($sql, array($this->Id, $larp->Id, $larp->Id));
    }
    
    public static function allForRole(Role $role, Larp $larp) {
        if (is_null($role)) return Array();
        $sql = "SELECT * FROM regsys_subdivision WHERE Id IN (SELECT SubdivisionId FROM  regsys_subdivisionmember WHERE RoleId=?) ORDER BY ".static::$orderListBy.";";
        $manual_subdivisions = static::getSeveralObjectsqQuery($sql, array($role->Id));
        $all_subdivisions = static::allByCampaign($larp);
        $member_subdivisions = array();
        foreach ($all_subdivisions as $subdivision) {
            if (in_array($subdivision, $manual_subdivisions)) $member_subdivisions[] = $subdivision;
            else {
                $members = $subdivision->getAllAutomaticRegisteredMembers($larp);
                if (in_array($role, $members)) $member_subdivisions[] = $subdivision;
            }
        }
        return $member_subdivisions;
    }
    
    public static function allVisibleForRole(Role $role, Larp $larp) {
        if (is_null($role)) return Array();
        $member_subdivisions = static::allForRole($role, $larp);
        $visible_subdivisions = array();
        foreach ($member_subdivisions as $subdivision) {
            if ($subdivision->isVisibleToParticipants()) $visible_subdivisions[] = $subdivision;
        }
        return $visible_subdivisions;
    }
    
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_subdivision WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    
    public function getAllKnownRoles(LARP $larp) {
        return Role::getAllKnownRolesForSubdivision($this, $larp);
    }
    
    public function getAllKnownGroups(LARP $larp) {
        return Group::getAllKnownGroupsForSubdivision($this, $larp);
    }
    
    public function getAllKnownNPCGroups(LARP $larp) {
        return IntrigueActor_KnownNPCGroup::getAllKnownNPCGroupsForSubdivision($this, $larp);
    }
    
    public function getAllKnownNPCs(LARP $larp) {
        return IntrigueActor_KnownNPC::getAllKnownNPCsForSubdivision($this, $larp);
    }
    
    public function getAllKnownProps(LARP $larp) {
        return IntrigueActor_KnownProp::getAllKnownPropsForSubdivision($this, $larp);
    }
    
    public function getAllKnownPdfs(LARP $larp) {
        return IntrigueActor_KnownPdf::getAllKnownPdfsForSubdivision($this, $larp);
    }
    
    public function getAllCheckinLetters(LARP $larp) {
        return IntrigueActor_CheckinLetter::getAllCheckinLettersForSubdivision($this, $larp);
    }
    
    public function getAllCheckinTelegrams(LARP $larp) {
        return IntrigueActor_CheckinTelegram::getAllCheckinTelegramsForSubdivision($this, $larp);
    }
    
    public function getAllCheckinProps(LARP $larp) {
        return IntrigueActor_CheckinProp::getAllCheckinPropsForSubdivision($this, $larp);
    }
    
    
    public static function getAllKnownSubdivisionsForRole(Role $role, LARP $larp) {
        $sql = "SELECT * FROM regsys_subdivision WHERE Id IN (".
            "SELECT iak.SubdivisionId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.RoleId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id, $larp->Id));
    }
    
    public static function getAllKnownSubdivisionsForGroup(Group $group, LARP $larp) {
        $sql = "SELECT * FROM regsys_subdivision WHERE Id IN (".
            "SELECT iak.SubdivisionId FROM regsys_intrigueactor_knownactor, regsys_intrigueactor as ias, regsys_intrigueactor as iak, regsys_intrigue WHERE ".
            "ias.GroupId = ? AND ".
            "ias.id = regsys_intrigueactor_knownactor.IntrigueActorId AND ".
            "regsys_intrigueactor_knownactor.KnownIntrigueActorId = iak.Id AND ".
            "ias.IntrigueId = regsys_intrigue.Id AND ".
            "regsys_intrigue.LarpId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($group->Id, $larp->Id));
    }
    
    public function IsFirstRole(Role $role, LARP $larp) {
        $members = $this->getAllRegisteredMembers($larp);
        if (!empty($members) && $members[0] == $role) return true;
        return false;
    }
    
    
}