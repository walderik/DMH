<?php

class Alchemy_Supplier_Ingredient extends BaseModel{
    
    public $Id;
    public $SupplierId;
    public $IngredientId;
    public $LARPId;
    public $Amount;
    public $IsApproved = 0;

    
    
    public static $orderListBy = 'SupplierId, IngredientId';
    
    
    public static function newFromArray($post){
        $object = static::newWithDefault();
        $object->setValuesByArray($post);
        return $object;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['SupplierId'])) $this->SupplierId = $arr['SupplierId'];
        if (isset($arr['IngredientId'])) $this->IngredientId = $arr['IngredientId'];
        if (isset($arr['LARPId'])) $this->LARPId = $arr['LARPId'];
        if (isset($arr['Amount'])) $this->Amount = $arr['Amount'];
        if (isset($arr['IsApproved'])) $this->IsApproved = $arr['IsApproved'];
    }
    
    
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        global $current_larp;
        $object = new self();
        $object->LARPId = $current_larp->Id;
        return $object;
    }
    
    # Update an existing object in db
    public function update() {
        $stmt = $this->connect()->prepare("UPDATE regsys_alchemy_supplier_ingredient SET Amount=?, IsApproved=? WHERE Id = ?");
        
        if (!$stmt->execute(array($this->Amount, $this->IsApproved, $this->Id))) {
                $stmt = null;
                header("location: ../index.php?error=stmtfailed");
                exit();
            }
            
            $stmt = null;
    }
    
    
    # Create a new object in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_alchemy_supplier_ingredient (SupplierId, IngredientId, LARPId, Amount,
            IsApproved) VALUES (?,?,?,?,?);");
        
        if (!$stmt->execute(array($this->SupplierId, $this->IngredientId, $this->LARPId, $this->Amount,
            $this->IsApproved))) {
                $this->connect()->rollBack();
                $stmt = null;
                header("location: ../participant/index.php?error=stmtfailed");
                exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
    
    public function isApproved() {
        if ($this->IsApproved == 1) return true;
        return false;
    }
   
    public function getIngredient() {
        return Alchemy_Ingredient::loadById($this->IngredientId);
    }
    
    public static function getIngredientAmountsForSupplier(Alchemy_Supplier $supplier, LARP $larp) {
        $sql = "SELECT * FROM regsys_alchemy_supplier_ingredient WHERE SupplierId=? AND LARPId=? ORDER BY ".static::$orderListBy.";";
        return static::getSeveralObjectsqQuery($sql, array($supplier->Id, $larp->Id));
    }
    
    
    
}