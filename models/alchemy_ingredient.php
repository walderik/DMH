<?php

class Alchemy_Ingredient extends BaseModel{
    
    public $Id;
    public $CampaignId;
    public $Name;
    public $Description;
    public $Level;
    public $IsCatalyst = 0;
    public $ActualIngredient;
    public $Info;
    public $IsApproved = 0;
    public $OrganizerNotes;
    
    const POINTS = [ 1=>1, 2=>2, 3=>4, 4=>8, 5=>20];
    
    public static $orderListBy = 'IsCatalyst, Name';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['Description'])) $this->Description = $arr['Description'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
        if (isset($arr['IsCatalyst'])) $this->IsCatalyst = $arr['IsCatalyst'];
        if (isset($arr['ActualIngredient'])) $this->ActualIngredient = $arr['ActualIngredient'];
        if (isset($arr['Info'])) $this->Info = $arr['Info'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
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
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_ingredient SET Name=?, Description=?, Level=?, IsCatalyst=?, ActualIngredient=?,
                    Info=?, IsApproved=?, OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Level, $this->IsCatalyst, $this->ActualIngredient,
            $this->Info, $this->IsApproved, $this->OrganizerNotes, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_alchemy_ingredient (CampaignId, Name, Description, Level,
            IsCatalyst, ActualIngredient, Info, IsApproved, OrganizerNotes) VALUES (?,?,?,?,?, ?,?,?,?);");
        
        if (!$stmt->execute(array($this->CampaignId, $this->Name, $this->Description, $this->Level,
            $this->IsCatalyst, $this->ActualIngredient,
            $this->Info, $this->IsApproved, $this->OrganizerNotes))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function allApprovedByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=1 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    

    public static function getIngredientsByLevel($level, LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=1 AND Level=? AND IsCatalyst=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $level));
    }
    
    public static function getIngredientsByEssenceLevel(Alchemy_Essence $essence, $level, LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=1 AND Level=? AND IsCatalyst=0 AND Id IN(".
            "SELECT IngredientId FROM regsys_alchemy_ingredient_essence WHERE EssenceId=?) ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $level, $essence->Id));
    }
    
    public static function getIngredientsByCatalystLevel($level, LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=1 AND Level=? AND IsCatalyst=1 ".
            "ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $level));
    }
    
    public static function getAllCatalysts(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=1 AND IsCatalyst=1 ORDER BY Level, Name";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function getAllIngredients(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=1 AND IsCatalyst=0 ORDER BY Level, Name";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    public function mayDelete() {
        //$sql = "SELECT * FROM regsys_alchemy_essence WHERE OppositeEssenceId=?";
        //$res = static::getSeveralObjectsqQuery($sql, array($this->Id));
        //if (!empty($res)) return false;
        
        //TODO lägg på fler kontroller här när det blir fler kopplingar
        return true;
    }
    
    public static function delete($id) {
        $ingredient = Alchemy_Ingredient::loadById($id);
        $ingredient->deleteEssences();
        parent::delete($id);
    }
    
    public function isApproved() {
        if ($this->IsApproved == 1) return true;
        return false;
    }
    public function isCatalyst() {
        if ($this->IsCatalyst == 1) return true;
        return false;
    }
    
    public function isIngredient() {
        return !$this->IsCatalyst();
    }
    
    public function getEssenceNames() {
        $essences = $this->getThreeEssences();
        $essenceNames = array();
        foreach ($essences as $essence) {
            if (is_null($essence)) continue;
            $essenceNames[] = $essence->Name;
        }
        return implode(", ", $essenceNames);
    }

    public function getEssenceIds() {
        $essences = $this->getThreeEssences();
        $essenceIds = array(null, null, null);
        foreach ($essences as $key => $essence) {
            if (is_null($essence)) continue;
            $essenceIds[$key] = $essence->Id;
        }
        return $essenceIds;
        
    }

    public function getEssences() {
        $sql = "SELECT * FROM regsys_alchemy_essence WHERE Id In (".
            "SELECT EssenceId FROM regsys_alchemy_ingredient_essence WHERE IngredientId = ?) ORDER BY ".Alchemy_Essence::$orderListBy.";";
        return Alchemy_Essence::getSeveralObjectsqQuery($sql, array($this->Id));
    }
    
    
    private function getThreeEssences() {
        $three_essences = array(null, null, null);
        
        $sql = "SELECT * FROM regsys_alchemy_essence WHERE Id In (".
            "SELECT EssenceId FROM regsys_alchemy_ingredient_essence WHERE IngredientId = ?) ORDER BY ".Alchemy_Essence::$orderListBy.";";
        $essences = Alchemy_Essence::getSeveralObjectsqQuery($sql, array($this->Id));
        $i = 0;
        foreach ($essences as $essence) {
            if ($i > 2) break;
            $three_essences[$i] = $essence;
            $i++;
        }
        return $three_essences;
    }
    
    public function setEssences($essenceIds) {
        $setEssences = array();
        foreach($essenceIds as $essenceId) {
            if (is_null($essenceId) || !is_numeric($essenceId) || in_array($essenceId, $setEssences)) continue;
            $setEssences[] = $essenceId;
            $stmt = $this->connect()->prepare("INSERT INTO regsys_alchemy_ingredient_essence (IngredientId, EssenceId) VALUES (?,?);");
            if (!$stmt->execute(array($this->Id, $essenceId))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteEssences() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_alchemy_ingredient_essence WHERE IngredientId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_alchemy_ingredient WHERE CampaignId=? AND IsApproved=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public static function getIngredientsForRecipe(Alchemy_Recipe $recipe) {
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE Id IN (".
        "SELECT IngredientId FROM regsys_alchemy_recipe_ingredient WHERE RecipeId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($recipe->Id));
    }
    
    public static function getMissingIngredientsForRecipe(Alchemy_Recipe $recipe, LARP $larp) {
        $sql = "SELECT * FROM regsys_alchemy_ingredient WHERE Id IN (".
            "SELECT regsys_alchemy_recipe_ingredient.IngredientId FROM regsys_alchemy_recipe_ingredient WHERE ".
            "regsys_alchemy_recipe_ingredient.RecipeId = ? AND ".
            "regsys_alchemy_recipe_ingredient.IngredientId  NOT IN (SELECT IngredientId FROM regsys_alchemy_supplier_ingredient WHERE ".
            "regsys_alchemy_supplier_ingredient.Amount > 0 AND ".
            "regsys_alchemy_supplier_ingredient.LarpId = ? ".
        ")) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($recipe->Id, $larp->Id));
    }
    
    public function countAtLarp(LARP $larp) {
        $sql = "SELECT Sum(Amount) as Num FROM regsys_alchemy_supplier_ingredient WHERE IngredientId=? AND LARPId=? AND IsApproved=1";
        return static::countQuery($sql, array($this->Id, $larp->Id));
    }
    
    
    public function hasIngredient(Alchemy_Supplier $supplier, Larp $larp) {
        $sql = "SELECT count(IngredientId) as Num FROM regsys_alchemy_supplier_ingredient WHERE IngredientId=? AND SupplierId=? AND LarpId=? AND IsApproved=1;";
        return static::existsQuery($sql, array($this->Id, $supplier->Id, $larp->Id));
        
    }
    
    public function wantsIngredient(Alchemy_Supplier $supplier, Larp $larp) {
        $sql = "SELECT count(IngredientId) as Num FROM regsys_alchemy_supplier_ingredient WHERE IngredientId=? AND SupplierId=? AND LarpId=? AND IsApproved=0;";
        return static::existsQuery($sql, array($this->Id, $supplier->Id, $larp->Id));
        
    }
    
    
}