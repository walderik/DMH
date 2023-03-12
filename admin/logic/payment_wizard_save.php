<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    
    $first_date=$_POST['first_date'];
    $last_date=$_POST['last_date'];
    $number_of_time_intervals=$_POST['number_of_time_intervals'];
    $min_age=$_POST['min_age'];
    $max_age=$_POST['max_age'];
    $number_of_age_groups=$_POST['number_of_age_groups'];
    
    if (isset($_POST['date'])) $dateArr = $_POST['date'];
    if (isset($_POST['age'])) $ageArr = $_POST['age'];
    if (isset($_POST['cost'])) $costMatrix = $_POST['cost'];

} else {
    header('Location: ../payment_information_admin.php');
    exit;
}

$payment_array = PaymentInformation::allBySelectedLARP();
foreach ($payment_array as $payment) {
    PaymentInformation::delete($payment->Id);
}
    


for ($i = 0; $i < $number_of_time_intervals; ++$i) {
     for ($j = 0; $j < $number_of_age_groups; ++$j) {
         if ($i == 0) {
             $from_date = $first_date;
         }
         else {
             $tmp_date=date_create($dateArr[$i-1]);
             $tmp_date->modify('+1 day');
             
             $from_date = $tmp_date->format('Y-m-d');
         }
         if ($j == 0) {
             $from_age = $min_age;
         }
         else {
             $from_age = $ageArr[$j-1]+1;
         }
         $paymentInformation = PaymentInformation::newWithDefault();
         $paymentInformation->FromDate = $from_date;
         $paymentInformation->ToDate = $dateArr[$i];
         $paymentInformation->FromAge = $from_age;
         $paymentInformation->ToAge = $ageArr[$j];
         $paymentInformation->Cost = $costMatrix[$i+1][$j+1];
         $paymentInformation->Create();         
     }
}

header('Location: ../payment_information_admin.php');