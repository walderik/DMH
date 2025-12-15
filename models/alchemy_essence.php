<?php

class Alchemy_Essence extends BaseModel{
    
    public $Id;
    public $CampaignId;
    public $Name;
    public $Description;
    public $Element;
    public $OppositeEssenceId;
    public $OrganizerNotes;
    
    
    public static $orderListBy = 'Name';
    
    
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
        if (isset($arr['Element'])) $this->Element = $arr['Element'];
        if (isset($arr['OppositeEssenceId'])) $this->OppositeEssenceId = $arr['OppositeEssenceId'];
        if (isset($arr['OrganizerNotes'])) $this->OrganizerNotes = $arr['OrganizerNotes'];
        
        if (isset($this->OppositeEssenceId) && $this->OppositeEssenceId=='null') $this->OppositeEssenceId = null;
        
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
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_essence SET Name=?, Description=?, Element=?, OppositeEssenceId=?, 
                    OrganizerNotes=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Name, $this->Description, $this->Element, $this->OppositeEssenceId,
            $this->OrganizerNotes, $this->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_alchemy_essence (CampaignId, Name, Description, Element, 
            OppositeEssenceId, OrganizerNotes) VALUES (?,?,?,?,?, ?);");
        
        if (!$stmt->execute(array($this->CampaignId, $this->Name, $this->Description, $this->Element, $this->OppositeEssenceId,
            $this->OrganizerNotes))) {
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
        $sql = "SELECT * FROM regsys_alchemy_essence WHERE CampaignId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($larp->CampaignId));
    }
    
    public function hasOppositeEssence() {
        if (isset($this->OppositeEssenceId)) return true;
        return false;
    }
    
    public function getOppositeEssence() {
        if ($this->hasOppositeEssence()) return Alchemy_Essence::loadById($this->OppositeEssenceId);
        return null;
    }
    
    public function mayDelete() {
        $sql = "SELECT * FROM regsys_alchemy_essence WHERE OppositeEssenceId=?";
        $res = static::getSeveralObjectsqQuery($sql, array($this->Id));
        if (!empty($res)) return false;
        
        $sql = "SELECT * FROM regsys_alchemy_recipe_essence WHERE EssenceId=?";
        $res = static::getSeveralObjectsqQuery($sql, array($this->Id));
        if (!empty($res)) return false;

        $sql = "SELECT * FROM regsys_alchemy_ingredient_essence WHERE EssenceId=?";
        $res = static::getSeveralObjectsqQuery($sql, array($this->Id));
        if (!empty($res)) return false;
        
        return true;
    }
   
    public static function getEssencesInRecipe(Alchemy_Recipe $recipe) {
        $sql = "SELECT * FROM regsys_alchemy_essence WHERE Id IN (SELECT EssenceId FROM regsys_alchemy_recipe_essence WHERE RecipeId=?)";
        return static::getSeveralObjectsqQuery($sql, array($recipe->Id));
    }
}