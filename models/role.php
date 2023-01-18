<?php

class Role extends BaseModel{
    
    public $Id;
    public $Name;
    public $IsNPC;
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
    public $CharactersWithRelations;
    
 
    
    public static $orderListBy = 'Name';
    

}