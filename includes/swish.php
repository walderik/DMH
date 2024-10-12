<?php

class Swish {
// Setup the data object for the payment

    public static function QRCode(Registration $registration, Campaign $campaign) {
        $data = [
            'format' => 'png',
            'size' => 300,
            'border' => 1,
            'payee' => ['value' => $campaign->SwishNumber, 'editable' => false],
            'message' => ['value' => $registration->PaymentReference, 'editable' => false],
            'amount' => ['value' => $registration->AmountToPay, 'editable' => false]
        ];
        
        // Convert the data array to JSON
        $data_json = json_encode($data);
        
        // Setup the cURL options
        $ch = curl_init("https://mpc.getswish.net/qrg-swish/api/v1/prefilled");
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_json)
        ]);
        
        
        // Execute the request
        $response = curl_exec($ch);
        return $response;

     }
    
    public static function getSwishLink(Registration $registration, Campaign $campaign) { 
        // Based on https://gist.github.com/filleokus/a8f1ffee4d49e09572aacd6239bc84cd
        
        
        $data = [
            'version' => '1',
            'payee' => ['value' => $campaign->SwishNumber, 'editable' => false],
            'message' => ['value' => $registration->PaymentReference, 'editable' => false],
            'amount' => ['value' => $registration->AmountToPay, 'editable' => false]
        ];
        
        
        $baseURL = "swish://payment?data=";
        return $baseURL . urlencode(json_encode($data));
    }
    
}
