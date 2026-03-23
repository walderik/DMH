<?php
declare(strict_types=1);

global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root . '/includes/all_includes.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;
use chillerlan\QRCode\Data\QRMatrix;
use chillerlan\QRCode\Output\QRImage;
use chillerlan\QRCode\Output\QRImagick;



// All kod som skall köras först på varje sida gemensamt oavsett om det rör admin-header eller annan header

$root = $_SERVER['DOCUMENT_ROOT'];



if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET['id'])) {
        require_once $root . '/includes/init.php';
        // If the user is not logged in redirect to the login page...
        if (!isset($_SESSION['is_loggedin'])) {
            header('Location: index.php');
            exit;
        }
        
        $imageId = $_GET['id'];
        $image = Image::loadById($imageId);
        
        ob_clean(); // rensa buffert
        header("Content-Type: ".$image->file_mime);
        echo $image->file_data;
        
    } elseif (isset($_GET['Swish'])) {
        include_once $root . '/includes/all_includes.php';
        echo "Swish<br>";

        $registration = Registration::loadById($_GET['RegistrationId']);
        $campaign = Campaign::loadById($_GET['CampaignId']);
        $response = Swish::QRCode($registration, $campaign);

        print_r($response);
        echo "<br>";
        //header('Content-Length: ' . strlen($response));
        
        /*
        $registration = Registration::loadById($_GET['RegistrationId']);
        $campaign = Campaign::loadById($_GET['CampaignId']);
        $response = Swish::QRCode($registration, $campaign);   
        
        ob_clean(); // rensa buffert
        header("Content-type: image/png");
        */
        echo $response;
        
    } elseif (isset($_GET['checkin'])) {
        
        
//         $imagick = new Imagick();
//         $imagick->newPseudoImage(100, 100, 'xc:red');
//         header('Content-Type: image/webp');
//         echo $imagick->getImageBlob();
//         exit;
        
        if (isset($_GET['example'])) {
            $link = "https://$_SERVER[HTTP_HOST]/checkin/example";
        } else {
            if (isset($_GET['encodedReference'])) $encodedReference = $_GET['encodedReference'];
            else {
                $registration = Registration::loadById($_GET['RegistrationId']);
                $encodedReference = base64_encode($registration->PaymentReference);
            }
            $link = "https://$_SERVER[HTTP_HOST]/checkin/person.php?code=".$encodedReference;
            
        }

        $options = new QROptions;
        
        $options = new QROptions([
            'version' => 7,
            'outputType' => 'png',
            'scale' => 20,
            'quality' => 90,
            'outputBase64' => false, 
        ]);
        
        $out = (new QRCode($options))->render($link);
        
        ob_clean();
        header('Content-Type: image/png');
        echo $out;
        exit;
        
      
    }
}




