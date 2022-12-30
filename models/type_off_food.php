<?php

include_once 'includes/selection_data.php';

//         bind_param
//     i	corresponding variable has type int
//     d	corresponding variable has type float
//     s	corresponding variable has type string
//     b	corresponding variable is a blob and will be sent in packets

class TypeOffFood extends SelectionData{
    
    public static $tableName = 'typesoffood';
      
}

?>