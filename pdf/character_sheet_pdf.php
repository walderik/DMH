<?php
# Läs mer på http://www.fpdf.org/

# För avgöra om intriger skall skrivas ut:
# $larp->isIntriguesReleased()

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'] . "/regsys";

require_once $root . '/includes/fpdf185/fpdf.php';
require_once $root . '/includes/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';


class CharacterSheet_PDF extends PDF_MemImage {
    
    public static $Margin = 5;
    
    public static $x_min = 5;
    public static $x_max = 205;
    public static $y_min = 5;
    public static $y_max = 291;
    
    public static $cell_y = 5;
    
    public static $header_fontsize = 6;
    public static $text_fontsize = 12;
    public static $text_max_length = 50;
    
    public $role;
    public $isReserve;
    public $person;
    public $isMyslajvare;
    public $larp;
    public $all;
    public $current_left;
    public $cell_y_space;        # Standardhöjden på en cell 
    public $current_cell_height; # Nuvarande höjden på den här radens celler
    public $cell_width;
    
    function Header() {
        global $root, $y, $mitten;
        $this->SetLineWidth(0.6);
        $this->Line(static::$x_min, static::$y_min, static::$x_max, static::$y_min);
        $this->Line(static::$x_min, static::$y_min, static::$x_min, static::$y_max);
        $this->Line(static::$x_min, static::$y_max, static::$x_max, static::$y_max);
        $this->Line(static::$x_max, static::$y_min, static::$x_max, static::$y_max);
        
        $space = 1.2;
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_max+$space, static::$y_min-$space);
        $this->Line(static::$x_min-$space, static::$y_min-$space, static::$x_min-$space, static::$y_max+$space);
        $this->Line(static::$x_min-$space, static::$y_max+$space, static::$x_max+$space, static::$y_max+$space);
        $this->Line(static::$x_max+$space, static::$y_min-$space, static::$x_max+$space, static::$y_max+$space);
        
        
        $this->SetXY($mitten-15, 3);
        $this->SetFont('Helvetica','',static::$text_fontsize/1.1);
        $this->SetFillColor(255,255,255);
        if ($this->all) {
            $txt = $this->role->Name;
        } else {
            $txt = 'Karaktärsblad';
        }
        $this->MultiCell(30, 4, utf8_decode($txt), 0, 'C', true);
        
        $y = static::$y_min + static::$Margin;
    }
    
    # Skriv ut lajvnamnet högst upp.
    function title($left, $text) {
        global $y;

        $font_size = (850 / strlen(utf8_decode($text)));
        if ($font_size > 90) $font_size = 90;
        $this->SetFont('Helvetica','B', $font_size);    # OK är Times, Arial, Helvetica
        
        $this->SetXY($left, $y-2);
        $this->Cell(0, static::$cell_y*5, utf8_decode($text),0,0,'C');
        
        $y = static::$y_min + (static::$cell_y*5) + (static::$Margin);
        
        $this->bar();
    }
    
    # Namnen på karaktär och spelare
    function names($left, $left2) {
        global $y, $mitten;
        
        $persn = $this->person;        
        $this->set_header($left, 'Spelare');
        
        if (empty($persn)) {
            $namn = 'Okänd (?)';
        } else {
            $age = $persn->getAgeAtLarp($this->larp);
            $namn = "$persn->Name ($age)";
        }
        $this->set_text($left, $namn);
        
        $this->mittlinje();
   
        $this->SetXY($left2, $y);
        $this->SetFont('Helvetica','',static::$header_fontsize);
        if ($this->isReserve) {
            $type = 'RESERV';
        } if ($this->role->IsDead) {
            $type = 'Avliden';
            $this->cross_over();
        } else {
            $type = ($this->role->isMain($this->larp)) ? 'Huvudkaraktär' : 'Sidokaraktär';
        }
         
        $this->Cell($this->cell_width, static::$cell_y, utf8_decode($type),0,0,'L');
        
        $font_size = (strlen($this->role->Name)>20) ? 14 : 24;
        $this->SetXY($left2, $y + static::$Margin + 1);
        $this->SetFont('Helvetica','B', $font_size); # Extra stora bokstäver på karaktärens namn
       
        $this->Cell($this->cell_width, static::$cell_y, utf8_decode($this->role->Name),0,0,'L');
    }

    
    function beskrivning() {
        global $y;
        $text = $this->role->Description;
        $this->set_rest_of_page('Beskrivning', $text);
        return true;
    }
    
    function intrigues() {
        global $y, $left, $left2;
        
        $space = 3;
        
        # Kolla först att det finns intriger och att någon har en intrigtext
        $intrigues = Intrigue::getAllIntriguesForRole($this->role->Id, $this->larp->Id);
        if (empty($intrigues)) return true;
        
        $tomma_intriger = true;
        foreach ($intrigues as $intrigue) {
            if (!$intrigue->isActive()) continue;
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this->role);   
            if (!empty($intrigueActor->IntrigueText)) $tomma_intriger = false;
        }
        if ($tomma_intriger) return true;
        
        
        $this->AddPage();
        $this->SetXY($left, $y);
        $this->SetFont('Helvetica','B',static::$text_fontsize);
        $this->Cell($this->cell_width, static::$cell_y, utf8_decode('Intriger'),0,0,'L');
        $y = $this->GetY()+$space*3;
        $this->SetXY($left, $y);
        
        $this->SetFont('Arial','',static::$text_fontsize);
        
        //point to end of the array
        end($intrigues);
        //fetch key of the last element of the array.
        $lastElementKey = key($intrigues);
        
        $known_actors = array();
        $known_npcs = array();
        $known_npcgroups = array();
        $known_props = array();
        
        foreach ($intrigues as $key => $intrigue) {
            if (!$intrigue->isActive()) continue;
            
            $intrigueActor = IntrigueActor::getRoleActorForIntrigue($intrigue, $this->role);
            if (empty($intrigueActor)) continue;

            $known_actors = array_merge($known_actors, $intrigueActor->getAllKnownActors());
            $known_npcs = array_merge($known_npcs, $intrigueActor->getAllKnownNPCs());
            $known_props = array_merge($known_props, $intrigueActor->getAllKnownProps());
            $known_npcgroups = array_merge($known_npcgroups, $intrigueActor->getAllKnownNPCGroups());
            
            if (!empty($intrigueActor->IntrigueText)) {
                $text = "($intrigue->Number) " . trim(utf8_decode($intrigueActor->IntrigueText));
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
            }
            
            if (!empty($intrigueActor->OffInfo)) {
                $text = trim(utf8_decode("OFF_INFORMATION: " . $intrigueActor->OffInfo));
                if (empty($intrigueActor->IntrigueText)) $text = "($intrigue->Number) " . $text;
                $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                $y = $this->GetY() + $space;
                $this->SetXY($left, $y);
            }
            
            if($key != $lastElementKey) {
                $this->bar();
                $y = $this->GetY()+$space*2;
                $this->SetXY($left, $y);
            }
        }
        
        # Dom man känner till från intrigerna
        if (!empty($known_actors)) {
            $this->bar();
            $y = $this->GetY()+$space*2;
            $this->SetXY($left, $y);
            
            $this->SetXY($left, $y);
            $this->SetFont('Helvetica','B',static::$text_fontsize);
            $this->Cell($this->cell_width, static::$cell_y, utf8_decode('Känner till'),0,0,'L');
            $y = $this->GetY()+$space*3;
            $this->SetXY($left, $y);
            $this->SetFont('Helvetica','',static::$text_fontsize);
            foreach ($known_actors as $known_actor) {
                # TODO skriv ut dom en per kolumn. Hur bred?
                $knownIntrigueActor = $known_actor->getKnownIntrigueActor();
                if (!empty($knownIntrigueActor->GroupId)) {
                    $groupActor=$knownIntrigueActor->getGroup();
                    $this->MultiCell(0, static::$cell_y-1, $groupActor->Name, 0, 'L');
                    $y = $this->GetY() + $space;
                    $this->SetXY($left, $y);
                } else {
                    $role = $knownIntrigueActor->getRole();
                    if ($role->hasImage()) {
                        $image = Image::loadById($role->ImageId);
                        $this->MemImage($image->file_data, $left, $y, 20);
                    }
                    $this->SetXY($left + 25, $y);
                    $text = $role->Name;
                    $role_group = $role->getGroup();
                    if (!empty($role_group)) $text .= " ($role_group->Name)";
                    $this->MultiCell(0, static::$cell_y-1, $text, 0, 'L');
                    $y = $this->GetY() + $space;
                    if ($role->hasImage()) $y += 25;
                    $this->SetXY($left, $y);
                    
                    
                    
                }
            }
        }
     

        return true;
    }
    
    function new_character_sheet(Role $role_in, LARP $larp_in, bool $all_information=false) {
        global $current_user, $x, $y, $left, $left2, $mitten;
        
        $this->role = $role_in;
        $this->person = $this->role->getPerson();
        $this->isMyslajvare = $this->role->isMysLajvare();
        $this->larp = $larp_in;
        $this->all = $all_information;
        $this->cell_y_space = static::$cell_y + (2*static::$Margin);
        $this->current_cell_height = $this->cell_y_space;
        
        $this->isReserve = Reserve_LARP_Role::isReserve($this->role->Id, $this->larp->Id);
        
        # Säkerställer att bara arrngörer någonsin kan få se all info om en karaktär
        if ($this->all && !(AccessControl::hasAccessCampaign($current_user->Id, $larp_in->CampaignId))) $this->all = false;
        
        $left = static::$x_min + static::$Margin;
        $x = $left;
        $this->cell_width = (static::$x_max - static::$x_min) / 2 - (2*static::$Margin);     
        $mitten = static::$x_min + (static::$x_max - static::$x_min) / 2 ;
        $left2 = $mitten + static::$Margin;
        
        $this->current_left = $left;
        
        $this->AddPage();
        
        $this->title($left, $this->larp->Name);
        $this->names($left, $left2);
        
        $y += $this->cell_y_space;
        
        $this->bar();
        
        # Uppräkning av ett antal fält som kan finnas eller inte
        $this->draw_field('epost');
        $this->draw_field('group');
        $this->draw_field('erfarenhet');
        $this->draw_field('yrke');
        $this->draw_field('lajvar_typ');
        $this->draw_field('rikedom');
        if ($this->all) $this->draw_field('intrigtyper');
        $this->draw_field('birth_place');
        if ($this->all) $this->draw_field('intrigsuggestions');
        $this->draw_field('bor');
        if ($this->all) $this->draw_field('notOkIntrigues');
        $this->draw_field('reason_for_being_in_here');
        
        if ($this->all) $this->draw_field('darkSecret');
        $this->draw_field('religion');
        if ($this->all) $this->draw_field('darkSecretSuggestion');
        
        if ($this->all) $this->draw_field('charactersWithRelations');
        if ($this->all) $this->draw_field('tidigareLajv');
       
        if ($this->all) $this->draw_field('organizerNotes');
        
        # Fixa till om vi skapat ett udda antal fält
        if ($this->current_left == $left2) $this->draw_field('empty');
        
        $this->beskrivning();

        # Att fixa när vi väl har tidigare lajv
        $previous_larps = $this->role->getPreviousLarps();
        if (isset($previous_larps) && count($previous_larps) > 0) {
            
            foreach ($previous_larps as $prevoius_larp) {
                $previous_larp_role = LARP_Role::loadByIds($this->role->Id, $prevoius_larp->Id);
                $this->AddPage();
                $this->title($left, "Historik $prevoius_larp->Name");

                $this->names($left, $left2);
                
                $text = (isset($prevoius_larp_role->Intrigue) && $prevoius_larp_role->Intrigue != "") ? $prevoius_larp_role->Intrigue : "Inget att rapportera";
                $this->set_rest_of_page("Intrig", $text);
                $y = $this->GetX();
                
               
                $text = (isset($prevoius_larp_role->WhatHappened) && $prevoius_larp_role->WhatHappened != "") ? $prevoius_larp_role->WhatHappened : "Inget att rapportera";
                $this->set_rest_of_page("Vad hände för ".$this->role->Name."?", $text);
                $y = $this->GetX();
                $this->bar();
                $text = (isset($prevoius_larp_role->WhatHappendToOthers) && $prevoius_larp_role->WhatHappendToOthers != "") ? $prevoius_larp_role->WhatHappendToOthers : "Inget att rapportera";
                $this->set_rest_of_page("Vad hände för andra?", $text);
            }
        }
        
        if (!$this->isReserve && ($this->larp->isIntriguesReleased() || $this->all)) {
            $this->intrigues();
        }
	}
	
	function all_character_sheets(LARP $larp_in ) {
	    $this->larp = $larp_in;

	    $roles = $this->larp->getAllMainRoles(false);
	    foreach($roles as $role) {
 	        $this->new_character_sheet($role, $larp_in, true);
	    }
	    $roles = $this->larp->getAllNotMainRoles(false);
	    foreach($roles as $role) {
	        $this->new_character_sheet($role, $larp_in, true);
	    }
	}
	
	# Dynamiska småfält
	
	protected function empty($left) {
	    $this->set_text($left, '');
	    return true;
	}
	
	protected function yrke($left) {
	    $this->set_header($left, 'Yrke');
	    $this->set_text($left, $this->role->Profession);
	    return true;
	}
	
	protected function epost($left) {
	    $this->set_header($left, 'Epost');
	    if (empty($this->person)) return true;
	    $this->set_text($left, $this->person->Email);
	    return true;
	}
	
	protected function erfarenhet($left) {
	    //TODO vet inte om detta ska vara valbart på lajvet
	    //if (!Experience::isInUse($this->larp)) return false;
	    
	    $this->set_header($left, 'Erfarenhet');
	    if (empty($this->person)) return true;
	    $this->set_text($left, $this->person->getExperience()->Name);
	    return true;
	}
	
	protected function rikedom($left) {
	    if (!Wealth::isInUse($this->larp)) return false;
	    
	    if ($this->isMyslajvare) return false;
	    
	    $this->set_header($left, 'Rikedom');
	    $text = ($this->role->is_trading($this->larp)) ? " (Handel)" : " (Ingen handel)";
	    $this->set_text($left, $this->role->getWealth()->Name . $text);
	    return true;
	}
	
	protected function intrigtyper($left) {
	    if (!IntrigueType::isInUse($this->larp)) return false;
	    
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Intrigtyper');

	    $text = commaStringFromArrayObject($this->role->getIntrigueTypes());
	    $this->set_text($left, $text);
	    return true;
	}
	
	protected function intrigsuggestions($left) {  
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Intrigförslag');

	    $this->set_text($left, $this->role->IntrigueSuggestions);
	    return true;
	}
	
	protected function notOkIntrigues($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Inget spel på');
	    $this->set_text($left, $this->role->NotAcceptableIntrigues);
	    return true;
	}
	
	protected function darkSecret($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Mörk hemlighet');
	    $this->set_text($left, $this->role->DarkSecret);
	    return true;
	}
	
	protected function darkSecretSuggestion($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Spel på Mörk hemlighet');
	    $this->set_text($left, $this->role->DarkSecretIntrigueIdeas);
	    return true;
	}
	
	protected function charactersWithRelations($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Viktigare personer');
	    $this->set_text($left, $this->role->CharactersWithRelations);
	    return true;
	}
	
	protected function tidigareLajv($left){
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Tidigare lajv');
	    $this->set_text($left, $this->role->PreviousLarps);
	    return true;
	}

	
	protected function OrganizerNotes($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Anteckning');
	    $this->set_text($left, $this->role->OrganizerNotes);
	    return true;
	}
	
	protected function lajvar_typ($left) {
	    if (!LarperType::isInUse($this->larp)) return false;
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Lajvartyp');
	    if (empty($this->person)) return true;
	    $mertext = (empty(trim($this->role->TypeOfLarperComment))) ? '' : " (".trim($this->role->TypeOfLarperComment).")";
	    $text = $this->role->getLarperType()->Name.$mertext;
	    $this->set_text($left, $text );
	    return true;
	}
	
	protected function group($left) {
	    $this->set_header($left, 'Grupp');
	    $group = $this->role->getGroup();
	    if (empty($group)) return true;
	    $this->set_text($left, $group->Name);
	    return true;
	}
	
	protected function birth_place($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Född');
	    $this->set_text($left, $this->role->Birthplace);
	    return true;
	}
	
	protected function bor($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Bor');
	    $this->set_text($left, $this->role->getPlaceOfResidence()->Name);
	    return true;
	}
	
	protected function religion($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Religion');
	    $this->set_text($left, $this->role->Religion);
	    return true;
	}
	
	protected function reason_for_being_in_here($left) {
	    if ($this->isMyslajvare) return false;
	    $this->set_header($left, 'Orsak för att vistas här');
	    $this->set_text($left, $this->role->ReasonForBeingInSlowRiver);
	    return true;
	}
	
	
	# Rita en ruta
	# Håll reda på om nästa ruta är till höger eller vänster
	private function draw_field($func) {
	    global $y, $left, $left2;
	    $to_execute = '$draw_ok = $this->'.$func.'($this->current_left);';
	    eval($to_execute);
	    if ($draw_ok) {
	        # Hantering om resultatet av cellen är för stort för att få plats.
	        $current_y = $this->GetY();
	        if ($current_y > $y + $this->current_cell_height) {
 	            $new_height = $current_y-$y;
 	            $this->current_cell_height = $new_height+2;
	        }
	        
	        # Räkna upp en cell i bredd
	        if ($this->current_left == $left) {
	            $this->current_left = $left2;
	        } else { 
	            # Vi har just ritat den högra rutan
	            $this->mittlinje();
	            $this->current_left = $left;
	            $y += $this->current_cell_height;
	            $this->bar();
	            $this->current_cell_height = $this->cell_y_space;
	        }
	    }
	}
	
	# Dra en linje tvärs över arket på höjd $y
	private function bar() {
	    global $y;
	    $this->Line(static::$x_min, $y, static::$x_max, $y);
	}
	
	private function mittlinje() {
	    global $y, $mitten;
	    $down = $y + $this->current_cell_height;
	    $this->Line($mitten, $y, $mitten, $down);
	}
	
	private function cross_over() {
	    global $y, $mitten;
	    $this->Line($this->current_left, $y+static::$Margin*1.5, ($this->current_left+$mitten-(3*static::$Margin)), $y+static::$Margin*1.5);
	}
	
// 	# Gemensamt sätt beräkna var rubriken i ett fält ska ligga
// 	private function set_header_start($venster) {
// 	    global $y;
// 	    $this->SetXY($venster, $y);
// 	    $this->SetFont('Helvetica','',static::$header_fontsize);
// 	}
	
	# Gemensam funktion för all logik för att skriva ut ett rubriken
	private function set_header($venster, $text) {
	    global $y;
	    $this->SetXY($venster, $y);
	    $this->SetFont('Helvetica','',static::$header_fontsize);
	    $this->Cell($this->cell_width, static::$cell_y, utf8_decode($text),0,0,'L');
	}
	
	# Gemensam funktion för all logik för att skriva ut ett fält
	private function set_text($venster, $text) {
	    global $y;
	    
	    if (empty($text)) return;
	    
	    $text = trim(utf8_decode($text));
	    
	    $this->SetXY($venster, $y + static::$Margin + 1);
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    
	    # Specialbehandling för väldigt långa strängar där vi inte förväntar oss det
	    
	    
	    if (strlen($text) > static::$text_max_length){
	        $this->SetXY($venster, $y + static::$Margin);
	        $this->SetFont('Arial','',static::$text_fontsize/1.25);
	        $this->MultiCell($this->cell_width+5, static::$cell_y-1, $text, 0, 'L');

	        return;
	    }
	    
	    $this->Cell($this->cell_width, static::$cell_y, $text, 0, 0, 'L');
	    
	    return;
	}
	
	private function set_full_page($header, $text) {
	    global  $y, $left;
	    
	    $this->AddPage();
	    
	    $text = utf8_decode($text);
	    $this->set_header($left, $header);
	    $this->SetXY($left, $y + static::$Margin+1);
	    
	    if (strlen($text)>3500){
	        $this->SetFont('Helvetica','',static::$header_fontsize);
	        $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y-2.5, $text, 0,'L'); # Mindre radavstånd
	        return;
	    }
	    if (strlen($text)>2900){
	        $this->SetFont('Helvetica','',static::$text_fontsize-1);
	        $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y-1.5, $text, 0,'L'); # Mindre radavstånd
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	}
	
	private function set_rest_of_page($header, $text) {
	    global  $y, $left;
	    
	    $text = utf8_decode($text);
	    $this->set_header($left, $header);
	    $this->SetXY($left, $y + static::$Margin+1);
	    
	    if (strlen($text)>1800){
	        $this->SetFont('Helvetica','',static::$text_fontsize/1.5); # Hantering för riktigt långa texter
	        $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y-1.3, $text, 0,'L');
	        return;
	    }
	    $this->SetFont('Helvetica','',static::$text_fontsize);
	    $this->MultiCell(($this->cell_width*2)+(2*static::$Margin), static::$cell_y+0.5, $text, 0,'L');
	}
	
	
}
