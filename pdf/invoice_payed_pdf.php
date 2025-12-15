<?php
# Läs mer på http://www.fpdf.org/

global $root, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];

require_once $root . '/lib/fpdf185/fpdf.php';
require_once $root . '/lib/fpdf185/script/mem_image.php';
require_once $root . '/includes/all_includes.php';

class Invoice_payed_PDF extends Invoice_PDF {
    
    
    function Header()
    {
        global $root, $current_larp;
        $omlogo = $root . 'images/'.$this->remove_swedish_chars($current_larp->getCampaign()->Abbreviation).'_logo_vit.jpg';
        $this->Image($omlogo, 10, 10, -200);
         //Put the watermark
         $this->SetFont('Arial','B',50);
         $this->SetTextColor(255,192,203);
         $this->SetDrawColor(255,192,203);
         $this->SetXY(70, 35);
         $this->MultiCell(70,20,encode_utf_to_iso("Betalad"),1,'C'); # 1- ger ram runt rutan så vi ser hur stor den är
    }
    
    function remove_swedish_chars($text) {
        $tmp = str_replace("Ö","O",$text);
        $tmp = str_replace("ö","o",$tmp);
        $tmp = str_replace("Ä","A",$tmp);
        $tmp = str_replace("ä","a",$tmp);
        $tmp = str_replace("Å","A",$tmp);
        $tmp = str_replace("å","a",$tmp);
        return $tmp;
    }
    
    
}
