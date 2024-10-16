<?php

class Evaluation extends BaseModel{
    
    public $Id;
    public $LarpId;
    public $Age;
    //public $Number_of_larps;
    public $larp_q1;
    public $larp_q2;
    public $larp_q3;
    public $larp_q4;
    public $larp_q5;
    public $larp_q6;
    public $larp_q7;
    public $larp_q8;
    public $larp_q9;
    public $larp_comment;
    public $exp_q1;
    public $exp_q2;
    public $exp_q3;
    public $exp_q4;
    public $exp_q5;
    public $exp_q6;
    public $exp_q7;
    public $exp_comment;
    public $info_q1;
    public $info_q2;
    public $info_q3;
    public $info_q4;
    public $info_dev;
    public $info_comment;
    public $food_q1;
    public $food_q2;
    public $food_comment;
    public $rules_q1;
    public $rules_q2;
    public $rules_q3;
    public $rules_comment;
    public $currency_q1;
    public $currency_q2;
    public $currency_comment;
    public $org_q1;
    public $org_q2;
    public $org_q3;
    public $org_q4;
    public $org_comment;
    public $health_q1;
    public $health_q2;
    public $health_q3;
    public $health_comment;
    public $game_q1;
    public $game_q2;
    public $game_q3;
    public $game_q4;
    public $game_q5;
    public $game_q6;
    public $game_q7;
    public $game_q8;
    public $game_q9;
    public $game_comment;
    public $finish_positive;
    public $finish_negative;
    public $finish_develop;
    public $finish_comment;
    
    
    
    
    
    public static $orderListBy = 'Id';
    
    public static function newFromArray($post){
        $obj = static::newWithDefault();
        $obj->setValuesByArray($post);
        return $obj;
    }
    
    public function setValuesByArray($arr) {
        if (isset($arr['Id'])) $this->Id = $arr['Id'];
        if (isset($arr['LarpId'])) $this->LarpId = $arr['LarpId'];
        if (isset($arr['Age'])) $this->Age = $arr['Age'];
        //if (isset($arr['Number_of_larps'])) $this->Number_of_larps = $arr['Number_of_larps'];
        if (isset($arr['larp_q1'])) $this->larp_q1 = $arr['larp_q1'];
        if (isset($arr['larp_q2'])) $this->larp_q2 = $arr['larp_q2'];
        if (isset($arr['larp_q3'])) $this->larp_q3 = $arr['larp_q3'];
        if (isset($arr['larp_q4'])) $this->larp_q4 = $arr['larp_q4'];
        if (isset($arr['larp_q5'])) $this->larp_q5 = $arr['larp_q5'];
        if (isset($arr['larp_q6'])) $this->larp_q6 = $arr['larp_q6'];
        if (isset($arr['larp_q7'])) $this->larp_q7 = $arr['larp_q7'];
        if (isset($arr['larp_q8'])) $this->larp_q8 = $arr['larp_q8'];
        if (isset($arr['larp_q9'])) $this->larp_q9 = $arr['larp_q9'];
        if (isset($arr['larp_comment'])) $this->larp_comment = $arr['larp_comment'];
        if (isset($arr['exp_q1'])) $this->exp_q1 = $arr['exp_q1'];
        if (isset($arr['exp_q2'])) $this->exp_q2 = $arr['exp_q2'];
        if (isset($arr['exp_q3'])) $this->exp_q3 = $arr['exp_q3'];
        if (isset($arr['exp_q4'])) $this->exp_q4 = $arr['exp_q4'];
        if (isset($arr['exp_q5'])) $this->exp_q5 = $arr['exp_q5'];
        if (isset($arr['exp_q6'])) $this->exp_q6 = $arr['exp_q6'];
        if (isset($arr['exp_q7'])) $this->exp_q7 = $arr['exp_q7'];
        if (isset($arr['exp_comment'])) $this->exp_comment = $arr['exp_comment'];
        if (isset($arr['info_q1'])) $this->info_q1 = $arr['info_q1'];
        if (isset($arr['info_q2'])) $this->info_q2 = $arr['info_q2'];
        if (isset($arr['info_q3'])) $this->info_q3 = $arr['info_q3'];
        if (isset($arr['info_q4'])) $this->info_q4 = $arr['info_q4'];
        if (isset($arr['info_dev'])) $this->info_dev = $arr['info_dev'];
        if (isset($arr['info_comment'])) $this->info_comment = $arr['info_comment'];
        if (isset($arr['food_q1'])) $this->food_q1 = $arr['food_q1'];
        if (isset($arr['food_q2'])) $this->food_q2 = $arr['food_q2'];
        if (isset($arr['food_comment'])) $this->food_comment = $arr['food_comment'];
        if (isset($arr['rules_q1'])) $this->rules_q1 = $arr['rules_q1'];
        if (isset($arr['rules_q2'])) $this->rules_q2 = $arr['rules_q2'];
        if (isset($arr['rules_q3'])) $this->rules_q3 = $arr['rules_q3'];
        if (isset($arr['rules_comment'])) $this->rules_comment = $arr['rules_comment'];
        if (isset($arr['currency_q1'])) $this->currency_q1 = $arr['currency_q1'];
        if (isset($arr['currency_q2'])) $this->currency_q2 = $arr['currency_q2'];
        if (isset($arr['currency_comment'])) $this->currency_comment = $arr['currency_comment'];
        if (isset($arr['org_q1'])) $this->org_q1 = $arr['org_q1'];
        if (isset($arr['org_q2'])) $this->org_q2 = $arr['org_q2'];
        if (isset($arr['org_q3'])) $this->org_q3 = $arr['org_q3'];
        if (isset($arr['org_q4'])) $this->org_q4 = $arr['org_q4'];
        if (isset($arr['org_comment'])) $this->org_comment = $arr['org_comment'];
        if (isset($arr['health_q1'])) $this->health_q1 = $arr['health_q1'];
        if (isset($arr['health_q2'])) $this->health_q2 = $arr['health_q2'];
        if (isset($arr['health_q3'])) $this->health_q3 = $arr['health_q3'];
        if (isset($arr['health_comment'])) $this->health_comment = $arr['health_comment'];
        if (isset($arr['game_q1'])) $this->game_q1 = $arr['game_q1'];
        if (isset($arr['game_q2'])) $this->game_q2 = $arr['game_q2'];
        if (isset($arr['game_q3'])) $this->game_q3 = $arr['game_q3'];
        if (isset($arr['game_q4'])) $this->game_q4 = $arr['game_q4'];
        if (isset($arr['game_q5'])) $this->game_q5 = $arr['game_q5'];
        if (isset($arr['game_q6'])) $this->game_q6 = $arr['game_q6'];
        if (isset($arr['game_q7'])) $this->game_q7 = $arr['game_q7'];
        if (isset($arr['game_q8'])) $this->game_q8 = $arr['game_q8'];
        if (isset($arr['game_q9'])) $this->game_q9 = $arr['game_q9'];
        if (isset($arr['game_comment'])) $this->game_comment = $arr['game_comment'];
        if (isset($arr['finish_positive'])) $this->finish_positive = $arr['finish_positive'];
        if (isset($arr['finish_negative'])) $this->finish_negative = $arr['finish_negative'];
        if (isset($arr['finish_develop'])) $this->finish_develop = $arr['finish_develop'];
        if (isset($arr['finish_comment'])) $this->finish_comment = $arr['finish_comment'];
        
    }
    
    # För komplicerade defaultvärden som inte kan sättas i class-defenitionen
    public static function newWithDefault() {
        return new self();
    }
    
    
    # Create a new evaluation in db
    public function create() {
        $connection = $this->connect();
        $stmt = $connection->prepare("INSERT INTO regsys_evaluation (LarpId, Age, 
            larp_q1, larp_q2, larp_q3, larp_q4, larp_q5, larp_q6, larp_q7, larp_q8, larp_q9, larp_comment, 
            exp_q1, exp_q2, exp_q3, exp_q4, exp_q5, exp_q6, exp_q7, exp_comment, 
            info_q1, info_q2, info_q3, info_q4, info_dev, info_comment, 
            food_q1, food_q2, food_comment,  
            rules_q1, rules_q2, rules_q3, rules_comment, 
            currency_q1, currency_q2, currency_comment,  
            org_q1, org_q2, org_q3, org_q4, org_comment,  
            health_q1, health_q2, health_q3, health_comment,  
            game_q1, game_q2, game_q3, game_q4, game_q5, game_q6, game_q7, game_q8, game_q9, game_comment, 
            finish_positive, finish_negative, finish_develop, finish_comment) 
            VALUES (?,?, 
            ?,?,?,?,?,?,?,?,?,?, 
            ?,?,?,?,?,?,?,?, 
            ?,?,?,?,?,?, 
            ?,?,?, 
            ?,?,?,?, 
            ?,?,?, 
            ?,?,?,?,?, 
            ?,?,?,?, 
            ?,?,?,?,?,?,?,?,?,?, 
            ?,?,?,?)");
       
        
        if (!$stmt->execute(array($this->LarpId, $this->Age, 
            $this->larp_q1, $this->larp_q2, $this->larp_q3, $this->larp_q4, $this->larp_q5, $this->larp_q6, $this->larp_q7, $this->larp_q8, $this->larp_q9, $this->larp_comment, 
            $this->exp_q1, $this->exp_q2, $this->exp_q3, $this->exp_q4, $this->exp_q5, $this->exp_q6, $this->exp_q7, $this->exp_comment,
            $this->info_q1, $this->info_q2, $this->info_q3, $this->info_q4, $this->info_dev, $this->info_comment, 
            $this->food_q1, $this->food_q2, $this->food_comment,
            $this->rules_q1, $this->rules_q2, $this->rules_q3, $this->rules_comment,
            $this->currency_q1, $this->currency_q2, $this->currency_comment,
            $this->org_q1, $this->org_q2, $this->org_q3, $this->org_q4, $this->org_comment, 
            $this->health_q1, $this->health_q2, $this->health_q3, $this->health_comment,
            $this->game_q1, $this->game_q2, $this->game_q3, $this->game_q4, $this->game_q5, $this->game_q6, $this->game_q7, $this->game_q8, $this->game_q9, $this->game_comment,
            $this->finish_positive, $this->finish_negative, $this->finish_develop, $this->finish_comment))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        $this->Id = $connection->lastInsertId();
        $stmt = null;
    }
    
}


class EvaluationNumberQuestion extends Dbh {
    public $number_of_responders;
    public $valuesArr = array(0,0,0,0,0,0,0,0,0,0,0);
    
    
    public static function get($question_id, LARP $larp) {
        $question_result = new self();
        
        $sql = "SELECT $question_id FROM regsys_evaluation WHERE LarpId = ?";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $question_result->number_of_responders = $stmt->rowCount();
        
        //Dela inte ut resultat om det är färre än 5 svar.
        if ($question_result->number_of_responders < 5) {
            $stmt = null;
            return $question_result;
        }
        
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $val = $row[$question_id];
            $question_result->valuesArr[$val]++;
        }
        $stmt = null;
        return $question_result;
    }
}

class EvaluationCommentsQuestion extends Dbh {
    public $number_of_responders;
    public $comments = array();
    
    
    public static function get($question_id, LARP $larp) {
        $question_result = new self();
        
        $sql = "SELECT $question_id FROM regsys_evaluation WHERE LarpId = ?";
        $stmt = static::connectStatic()->prepare($sql);
        
        if (!$stmt->execute(array($larp->Id))) {
            $stmt = null;
            header("location: ../index.php?error=stmtfailed");
            exit();
        }
        
        $question_result->number_of_responders = $stmt->rowCount();
        
        //Dela inte ut resultat om det är färre än 5 svar.
        if ($question_result->number_of_responders < 5) {
         $stmt = null;
         return $question_result;
         }
         
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as $row) {
            $question_result->comments[] = $row[$question_id];
        }
        $stmt = null;
        return $question_result;
    }
}
