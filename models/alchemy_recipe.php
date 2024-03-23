<?php

class Alchemy_Recipe extends BaseModel{
    
    public $Id;
    public $Name;
    public $AlchemistType;
    public $Preparation;
    public $Effect;
    public $SideEffect;
    public $IsApproved = 0;
    public $IsSecret = 0;
    public $OrganizerNotes;
    public $Level;
    public $CampaignId;
    public $AuthorRoleId;
    
    const LEVEL_REQUIREMENTS = [    1=>"2 p Katalysator nivå 1", 
                                    2=>"6 p, minst en ingrediens på nivå 2, Katalysator nivå 2", 
                                    3=>"12 p, minst en ingrediens på nivå 3, Katalysator nivå 3",
                                    4=>"24 p, minst en ingrediens på nivå 4, Katalysator nivå 4",
                                    5=>"60 p, minst en ingrediens på nivå 5, Katalysator nivå 5"
    ];
    
    public static $orderListBy = 'Name';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['Name'])) $this->Name = $arr['Name'];
        if (isset($arr['AlchemistType'])) $this->AlchemistType = $arr['AlchemistType'];
        if (isset($arr['Preparation'])) $this->Preparation = $arr['Preparation'];
        if (isset($arr['Effect'])) $this->Effect = $arr['Effect'];
        if (isset($arr['SideEffect'])) $this->SideEffect = $arr['SideEffect'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
        if (isset($arr['IsSecret'])) $this->IsSecret = $arr['IsSecret'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        if (isset($arr['Level'])) $this->Level = $arr['Level'];
        if (isset($arr['CampaignId'])) $this->CampaignId = $arr['CampaignId'];
        if (isset($arr['AuthorRoleId'])) $this->AuthorRoleId = $arr['AuthorRoleId'];
    }
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $object = new self();
        $object->CampaignId = $current_larp->CampaignId;
        $object->AlchemistType = Alchemy_Alchemist::INGREDIENT_ALCHEMY;
        return $object;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_recipe SET Name=?, AlchemistType=?, Preparation=?, Effect=?, SideEffect=?, 
                IsApproved=?, IsSecret=?, Level=?, OrganizerNotes=?, AuthorRoleId=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->AlchemistType, $this->Preparation, $this->Effect, $this->SideEffect, 
            $this->IsApproved, $this->IsSecret, $this->Level, $this->OrganizerNotes, $this->AuthorRoleId, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_alchemy_recipe (Name, AlchemistType, Preparation, Effect, SideEffect, 
            IsApproved, IsSecret, Level, OrganizerNotes, CampaignId, AuthorRoleId) VALUES (?,?,?,?,?, ?,?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->Name, $this->AlchemistType, $this->Preparation, $this->Effect, $this->SideEffect,
            $this->IsApproved, $this->IsSecret, $this->Level, $this->OrganizerNotes, $this->CampaignId, $this->AuthorRoleId))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
            $this->Id = $connection->lastInsertId();
            $stmt = null;
    }
    
    public function getAllAlchemists() {
        return Alchemy_Alchemist::getAlchemistsThatKnowsRecipe($this);
    }
    
    public function isApproved() {
        if ($this->IsApproved == 1) return true;
        return false;
    }
    
    public function isSecret() {
        if ($this->IsSecret == 1) return true;
        return false;
    }
    
    public function mayDelete() {
        $alchemists = $this->getAllAlchemists();
        if (empty($alchemists)) return true;
        return false;
    }
    
    public function getRecipeType() {
        if (!isset($this->AlchemistType)) return null;
        return Alchemy_Alchemist::ALCHEMY_TYPES[$this->AlchemistType];
    }
    
    public function getAuthorRole() {
        if (!isset($this->AuthorRoleId)) return null;
        return Role::loadById($this->AuthorRoleId);
    }
    
    
    public static function getRecipesForAlchemist(Alchemy_Alchemist $alchemist) {
        $sql = "SELECT * FROM regsys_alchemy_recipe WHERE Id IN (".
            "SELECT AlchemyRecipelId FROM regsys_alchemy_alchemist_recipe WHERE AlchemyAlchemistId=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($alchemist->Id));
    }
 
    public static function allByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_recipe WHERE CampaignId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    public static function allNotSecretByCampaign(LARP $larp) {
        if (is_null($larp)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_recipe WHERE CampaignId = ? AND IsSecret=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    
    public static function allByRole(Role $role) {
        if (is_null($role)) return Array();
        $sql = "SELECT * FROM regsys_alchemy_recipe WHERE AuthorRoleId = ? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($role->Id));
    }
    
    
    public function addToAlchemists($alchemistIds, LARP $larp) {
        //Ta reda på vilka som inte redan är kopplade till receptet
        $exisitingIds = array();
        $recipe_alchemists = $this->getAllAlchemists();
        foreach ($recipe_alchemists as $recipe_alchemist) {
            $exisitingIds[] = $recipe_alchemist->Id;
        }
        
        $newAlchemistIds = array_diff($alchemistIds,$exisitingIds);
        //Koppla recept till alkemist
        foreach ($newAlchemistIds as $alchemistId) {
            $this->addToAlchemist($alchemistId, $larp);
        }
    }
    
    private function addToAlchemist($alchemistId, LARP $larp) {
        
        $stmt = $this->connect()->prepare("INSERT INTO ".
            "regsys_alchemy_alchemist_recipe (AlchemyAlchemistId, AlchemyRecipelId, GrantedLarpId) VALUES (?,?,?);");
        if (!$stmt->execute(array($alchemistId, $this->Id, $larp->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    public function getComponentNames() {
        $names = array();
        if ($this->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
            $ingredients = Alchemy_Ingredient::getIngredientsForRecipe($this);

            
            foreach ($ingredients as $ingredient) {
                if ($ingredient->isCatalyst()) $names[] = "$ingredient->Name (Katalysator $ingredient->Level)";
                else $names[] = "$ingredient->Name ($ingredient->Level)";
            }
        } elseif ($this->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
            $essences = Alchemy_Essence::all();
            
            $selectedEssences = $this->getSelectedEssenceIds();
            foreach($selectedEssences as $selectedEssenceArr) {
                $selectedEssence = null;
                foreach ($essences as $essence) {
                    if ($essence->Id == $selectedEssenceArr[0]) {
                        $selectedEssence = $essence;
                        break;
                    }
                }
                
                $names[] = "$selectedEssence->Name (".$selectedEssenceArr[1].")";
            }
            $names[] = "Katalysator ($this->Level)";
        }
        
        return implode(", ", $names);
    }
    
    public function getSelectedIngredients() {
        return Alchemy_Ingredient::getIngredientsForRecipe($this);
    }
    
    public function getSelectedIngredientIds() {
        $stmt = $this->connect()->prepare("SELECT IngredientId FROM  regsys_alchemy_recipe_ingredient WHERE RecipeId = ? ORDER BY IngredientId;");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = $row['IngredientId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    public function saveAllIngredients($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $stmt = $this->connect()->prepare("INSERT INTO regsys_alchemy_recipe_ingredient (IngredientId, RecipeId) VALUES (?,?);");
            if (!$stmt->execute(array($Id, $this->Id))) {
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
            }
        }
        $stmt = null;
    }
    
    public function deleteAllIngredients() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_alchemy_recipe_ingredient WHERE RecipeId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    
    public function getSelectedEssencesPerLevelIds($level) {
        $stmt = $this->connect()->prepare("SELECT EssenceId FROM  regsys_alchemy_recipe_essence WHERE RecipeId = ? AND Level=?");
        
        if (!$stmt->execute(array($this->Id, $level))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = $row['EssenceId'];
        }
        $stmt = null;
        
        return $resultArray;
    }
        
  
    public function getSelectedEssenceIds() {
        $stmt = $this->connect()->prepare("SELECT EssenceId, Level FROM  regsys_alchemy_recipe_essence WHERE RecipeId = ? ORDER BY Level");
        
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        if ($stmt->rowCount() == 0) {
            $stmt = null;
            return array();
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $resultArray = array();
        foreach ($rows as $row) {
            $resultArray[] = array($row['EssenceId'], $row['Level']);
        }
        $stmt = null;
        
        return $resultArray;
    }
    
    
    
     public function saveAllEssences($idArr) {
        if (!isset($idArr)) {
            return;
        }
        foreach($idArr as $Id) {
            $info = explode("_", $Id);
            $this->saveEssence($info[1], $info[0]);
         }
    }
    
    private function saveEssence($essenceId, $level) {
        $stmt = $this->connect()->prepare("INSERT INTO regsys_alchemy_recipe_essence (RecipeId, EssenceId, Level) VALUES (?,?,?);");
        if (!$stmt->execute(array($this->Id, $essenceId, $level))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public function deleteAllEssences() {
        $stmt = $this->connect()->prepare("DELETE FROM regsys_alchemy_recipe_essence WHERE RecipeId = ?;");
        if (!$stmt->execute(array($this->Id))) {
            $stmt = null;
            header("location: ../participant/index.php?error=stmtfailed");
            exit();
        }
        $stmt = null;
    }
    
    public static function getAllToApprove(Larp $larp) {
        if (is_null($larp)) return array();
        $sql = "SELECT * from regsys_alchemy_recipe WHERE CampaignId=? AND IsApproved=0 ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
   
    
    public function calculatePoints() {
        $sum = 0;
        if ($this->AlchemistType == Alchemy_Alchemist::INGREDIENT_ALCHEMY) {
            $ingredients = $this->getSelectedIngredients();
            if (empty($ingredients)) return 0;
            foreach($ingredients as $ingredient) {
                $sum += Alchemy_Ingredient::POINTS[$ingredient->Level];
            }
        } elseif ($this->AlchemistType == Alchemy_Alchemist::ESSENCE_ALCHEMY) {
            $essences = $this->getSelectedEssenceIds();
            if (empty($essences)) return 0;
            $sum = 0;
            foreach($essences as $essence) {
                $sum += Alchemy_Ingredient::POINTS[$essence[1]];
            }
        }
        return $sum;
    }
    
    public static function allContainingIngredient(Alchemy_Ingredient $ingredient, LARP $larp) {
        if (is_null($ingredient)) return array();
        $sql = "SELECT * from regsys_alchemy_recipe WHERE CampaignId=? AND IsApproved=1 AND Id IN (".
                "SELECT RecipeId FROM regsys_alchemy_recipe_ingredient WHERE IngredientId = ?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $ingredient->Id));
    }
 
    public static function allContainingEssence(Alchemy_Essence $essence, $level, LARP $larp) {
        if (is_null($essence)) return array();
        $sql = "SELECT * from regsys_alchemy_recipe WHERE CampaignId=? AND IsApproved=1 AND Id IN (".
            "SELECT RecipeId FROM regsys_alchemy_recipe_essence WHERE EssenceId = ? AND Level=?) ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $essence->Id, $level));
    }

    
    public static function allContainingCatalystLevel($level, LARP $larp) {
        $sql = "SELECT * from regsys_alchemy_recipe WHERE CampaignId=? AND IsApproved=1 AND Level=? AND AlchemistType=".Alchemy_Alchemist::ESSENCE_ALCHEMY." ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId, $level));
    }
    
}