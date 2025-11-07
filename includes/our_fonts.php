<?php 

class OurFonts {
    
    public static function fontArray() {
        
        global $root;
        $root = $_SERVER['DOCUMENT_ROOT'];
        $path = $root . '/lib/fpdf185/font';

        $files = array_diff(scandir($path), array('.', '..'));
        
        $out_files = Array();
        foreach ($files as $file) {
            if (!str_ends_with($file, '.php')) continue;
            $font_name = substr($file, 0, -4);
            if (str_ends_with($font_name, 'b')) continue;
            if (str_ends_with($font_name, 'i')) continue;
            if (str_ends_with($font_name, 'bi')) continue;
            $out_files[] = $font_name;
        }
        sort($out_files, SORT_NATURAL  | SORT_FLAG_CASE );
        return $out_files;
    }
    
    # Dom här fonterna ingår inte i de inkluderade PDF-fonterna och måste läsas in med $pdf->AddFont('FontName','');
    # Alltså dom fonterna vi lagt till själva i font-biblioteket
    public static function fontsToLoad() {
        global $root;
        $root = $_SERVER['DOCUMENT_ROOT'];
        $path = $root . '/lib/fpdf185/font';
        
        $files = array_diff(scandir($path), array('.', '..'));
        
        $out_files = Array();
        foreach ($files as $file) {
            if (!str_ends_with($file, '.z')) continue;
            $out_files[] = substr($file, 0, -2);
        }
        sort($out_files, SORT_NATURAL  | SORT_FLAG_CASE );
        return $out_files;
    }
    
}
