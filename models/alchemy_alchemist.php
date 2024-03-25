<?php

class Alchemy_Alchemist extends BaseModel{
    
    const INGREDIENT_ALCHEMY = 0;
    const ESSENCE_ALCHEMY = 1;
    
    const ALCHEMY_TYPES = [
        Alchemy_Alchemist::INGREDIENT_ALCHEMY => "Traditionell",
        Alchemy_Alchemist::ESSENCE_ALCHEMY => "Experimentell"
    ];
    
    public $Id;
    public $RoleId;
    public $Level;
    public $AlchemistType;
    public $ImageId;
    public $AlchemistTeacherId;
    public $Workshop;
    public $OrganizerNotes;
    
    public static $orderListBy = 'Level';
    

    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['RoleId'])) $this->RoleId = $arr['RoleId'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
        if (isset($arr['AlchemistType'])) $this->AlchemistType = $arr['AlchemistType'];
        if (isset($arr['ImageId'])) $this->ImageId = $arr['ImageId'];
        if (isset($arr['AlchemistTeacherId'])) $this->AlchemistTeacherId = $arr['AlchemistTeacherId'];
        if (isset($arr['Workshop'])) $this->Workshop = $arr['Workshop'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        
        if (isset($this->ImageId) && $this->ImageId=='null') $this->ImageId = null;
        
        if (isset($this->Workshop) && ($this->Workshop=='0000-00-00' || $this->Workshop=='')) $this->Workshop = null;
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_alchemist SET RoleId=?, AlchemistType=?, ImageId=?, 
                AlchemistTeacherId=?, Workshop=?, Level=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->RoleId, $this->AlchemistType, $this->ImageId, 
            $this->AlchemistTeacherId, $this->Workshop, $this->Level, $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_alchemy_alchemist (RoleId, AlchemistType, ImageId, 
                    AlchemistTeacherId, Workshop, Level, OrganizerNotes) VALUES (?,?,?,?,?, ?,?);");
        
        if (!$stmt->execute(array($this->RoleId, $this->AlchemistType, $this->ImageId, $this->AlchemistTeacherId, $this->Workshop, $this->Level, $this->OrganizerNotes))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $stmt = null;
    }
    
    public static function getForRole(Role $role) {
        if (empty($role)) return null;
        $sql = "SELECT * FROM regsys_alchemy_alchemist WHERE RoleId=?";
        return static::getOneObjectQuery($sql, array($role->Id));
        
    }
    
    public static function isAlchemist(Role $role) {
        if (empty($role)) return null;
        if (is_null(static::getForRole($role))) return false;
        return true;
    }
    
    
    
    public function getRole() {
        if (empty($this->RoleId)) return null;
        return Role::loadById($this->RoleId);
    }
    
    public function getAlchemistType() {
        if (!isset($this->AlchemistType)) return null;
        return Alchemy_Alchemist::ALCHEMY_TYPES[$this->AlchemistType];
    }
    
    

    public function hasDoneWorkshop() {
        if (empty($this->Workshop)) return false;
        return true;
    }
    
    
    public function getTeacher() {
        if (empty($this->AlchemistTeacherId)) return null;
        return Alchemy_Alchemist::loadById($this->AlchemistTeacherId);
    }
    
    public function getStudents() {
        $sql = "SELECT * FROM regsys_alchemy_alchemist WHERE AlchemistTeacherId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($this->Id));
        
    }
    public function recipeApprovedLarp(Alchemy_Recipe $recipe) {
        $stmt = $this->connect()->prepare("SELECT GrantedLarpId FROM  regsys_alchemy_alchemist_recipe WHERE AlchemyAlchemistId=? AND AlchemyRecipelId = ?;");
        
        if (!$stmt->execute(array($this->Id, $recipe->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows[0]['GrantedLarpId'];
    }
 
    public function grantRecipe($recipeId, LARP $larp) {
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_alchemist_recipe SET GrantedLarpId=? WHERE AlchemyAlchemistId=? AND AlchemyRecipelId = ?;");
        
        if (!$stmt->execute(array($larp->Id, $this->Id, $recipeId))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    public function getRecipes($only_approved) {
        return Alchemy_Recipe::getRecipesForAlchemist($this, $only_approved);
    }
    
    public function addRecipes($recipeIds, Larp $larp, $isApproved) {
        //Ta reda på vilka som inte redan är kopplade till alkemisten
        $exisitingIds = array();
        $alchemist_recipes = $this->getRecipes(false);
        foreach ($alchemist_recipes as $alchemist_recipe) {
            $exisitingIds[] = $alchemist_recipe->Id;
        }
        
        $newRecipeIds = array_diff($recipeIds,$exisitingIds);
        //Koppla magier till magiker
        foreach ($newRecipeIds as $recipeId) {
            $this->addRecipe($recipeId, $larp, $isApproved);
        }
    }
    
    
    private function addRecipe($recipeId, LARP $larp, $isApproved) {
        if ($isApproved) {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_alchemy_alchemist_recipe (AlchemyAlchemistId, AlchemyRecipelId, GrantedLarpId) VALUES (?,?,?);");
            if (!$stmt->execute(array($this->Id, $recipeId, $larp->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
        } else {
            $stmt = $this->connect()->prepare("INSERT INTO ".
                "regsys_alchemy_alchemist_recipe (AlchemyAlchemistId, AlchemyRecipelId) VALUES (?,?);");
            if (!$stmt->execute(array($this->Id, $recipeId))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            
        }
         $stmt = null;
    }
    
    
    
    public function removeRecipe($recipeId) {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_alchemy_alchemist_recipe WHERE AlchemyAlchemistId=? AND AlchemyRecipelId=?;");
        if (!$stmt->execute(array($this->Id, $recipeId))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function getGrantedLarp(Alchemy_Recipe $recipe) {
        if (empty($recipe)) return null;
        $sql = "SELECT * FROM regsys_larp WHERE Id IN (".
            "SELECT GrantedLarpId FROM regsys_alchemy_alchemist_recipe WHERE AlchemyAlchemistId=? AND AlchemyRecipelId=?)";
        return LARP::getOneObjectQuery($sql, array($this->Id, $recipe->Id));
    }
    
    public static function getAlchemistsThatKnowsRecipe(Alchemy_Recipe $recipe) {
        $sql = "SELECT * FROM regsys_alchemy_alchemist WHERE Id IN (".
            "SELECT AlchemyAlchemistId FROM regsys_alchemy_alchemist_recipe WHERE AlchemyRecipelId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($recipe->Id));
    }
    
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_alchemist WHERE RoleId In (
            SELECT Id FROM regsys_role WHERE CampaignId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function RoleIdsByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT RoleId as Id FROM regsys_alchemy_alchemist WHERE RoleId In (
            SELECT Id FROM regsys_role WHERE CampaignId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getIdArray($sql, array($larp->CampaignId));
    }
    
    public static function getRolesByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_role WHERE Id IN (
            SELECT RoleId FROM regsys_alchemy_alchemist WHERE CampaignId = ?) ORDER BY Name";
        return Role::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    
    public static function createAlchemists($roleIds, LARP $larp) {
        //Ta reda på vilka som inte redan är alkemister
        $exisitingRoleIds = array();
        $alchemists = static::allByCampaign($larp);
        foreach ($alchemists as $alchemist) {
            $exisitingRoleIds[] = $alchemist->RoleId;
        }
        
        $newRoleIds = array_diff($roleIds,$exisitingRoleIds);
        foreach ($newRoleIds as $roleId) {
            $alchemist = Alchemy_Alchemist::newWithDefault();
            $alchemist->RoleId = $roleId;
            $alchemist->create();
        }
    }
    
    public function hasEquipmentImage() {
        if (isset($this->ImageId)) return true;
        return false;
    }
    
 
    public static function delete($id)
    {
        $alchemist = static::loadById($id);
        
        $recipes = $alchemist->getRecipes(false);
        if (isset($recipes)) {
            foreach($recipes as $recipes) {
                $alchemist->removeRecipe($recipes->Id);
            }
        }
        $imageId=$alchemist->ImageId;
        
        parent::delete($id);
    
        //Ta bort den bilden
        if (isset($imageId)) Image::delete($imageId);
    }
    
    public function recipeListApproved() {
        $sql = "SELECT Count(Id) as Num FROM regsys_alchemy_alchemist_recipe WHERE GrantedLarpId IS NULL AND AlchemyAlchemistId=?;";
        return !static::existsQuery($sql, array($this->Id));
    }
    
 
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT regsys_alchemy_alchemist.* from regsys_alchemy_alchemist, regsys_larp_role WHERE regsys_alchemy_alchemist.Id IN (".
            "SELECT AlchemyAlchemistId FROM regsys_alchemy_alchemist_recipe WHERE GrantedLarpId IS NULL) AND ".
            "regsys_larp_role.RoleId = regsys_alchemy_alchemist.RoleId AND ".
            "regsys_larp_role.LarpId=? ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    public static function allByComingToLarp(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_alchemist WHERE RoleId In (".
            "SELECT regsys_role.Id FROM regsys_role, regsys_larp_role, regsys_registration WHERE ".
            "regsys_role.Id = regsys_larp_role.RoleId AND ".
            "regsys_larp_role.LARPId = ? AND ".
            "regsys_larp_role.LARPId = regsys_registration.LarpId AND ".
            "regsys_registration.PersonId = regsys_role.PersonId AND ".
            "regsys_registration.NotComing = 0".
            ") ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->Id));
    }
    
    
    
    
}