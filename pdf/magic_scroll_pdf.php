<?php
# Läs mer på http://www.fpdf.org/

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/fpdf185/fpdf.php';
require $root . '/includes/init.php';


class MagicScroll_PDF extends FPDF {
    
    public $handfonts = ['xiparoslombard','YourRoyalMajesty','UncialAntiqua','TypographerRotunda','medievalsharp','rocksalt','Goticabastard',
        'ComicRunes'];
    
    public $fonts = ['xiparoslombard','YourRoyalMajesty','UncialAntiqua','TypographerRotunda','medievalsharp','rocksalt','Goticabastard',
        'ComicRunes'];
    
    function Header()
    {
        global $root;
        $this->Image($root . '/images/scroll.jpeg',null,null,208);
    }
    
 
    function PrintScroll(Magic_Spell $spell, $larpname) {
        $this->SetMargins(0, 0);
        $this->SetAutoPageBreak(false);
        $this->AddPage();
        
        //$font = $this->handfonts[array_rand($this->handfonts, 1)];
        $font = 'rocksalt';
        
        $this->AddFont($font);
        $size = 24;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 115);
        $this->MultiCell(150,6,encode_utf_to_iso($spell->Name),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        
        $size = 20;
        if (strlen($spell->Description) > 300) $size = 18;
        if (strlen($spell->Description) > 400) $size = 14;
        if (strlen($spell->Description) > 500) $size = 12;
        if (strlen($spell->Description) > 700) $size = 10;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 135);
        $this->MultiCell(150,10,encode_utf_to_iso($spell->Description),0,'L'); # 1- ger ram runt rutan så vi ser hur stor den är
        

        $size = 8;
        $this->SetFont($font,'',$size);
        $this->SetXY(30, 250);
        $this->MultiCell(150,6,encode_utf_to_iso($larpname),0,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
        
    }
     
}

