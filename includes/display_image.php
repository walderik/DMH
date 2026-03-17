<?php
global $root;
$root = $_SERVER['DOCUMENT_ROOT'];
include_once $root . '/includes/all_includes.php';
use chillerlan\QRCode\{QRCode, QROptions};

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
        $registration = Registration::loadById($_GET['RegistrationId']);
        $campaign = Campaign::loadById($_GET['CampaignId']);
        $response = Swish::QRCode($registration, $campaign);   
        
        ob_clean(); // rensa buffert
        header("Content-type: image/png");
        
        echo $response;
        
    } elseif (isset($_GET['checkin'])) {
        if (isset($_GET['example'])) {
            $link = "https://$_SERVER[HTTP_HOST]/checkin/example";
        } else {
            $registration = Registration::loadById($_GET['RegistrationId']);
            $link = "https://$_SERVER[HTTP_HOST]/checkin/person.php?code=".base64_encode($registration->PaymentReference);
        }

        ob_clean(); // rensa buffert
        header("Content-type: image/png");
        echo (new QRCode)->render($link);
        
        //		<?php printf('<img width=300px" src="%s" alt="QR Code" />', $registration->getQRcode()); 
   
//             public function getQRcode() {
//         $link = "https://$_SERVER[HTTP_HOST]/checkin/person.php?code=".base64_encode($this->PaymentReference);
//         return (new QRCode)->render($link);
//     }
    
//     public static function getExampleQRcode() {
//         $link = "https://$_SERVER[HTTP_HOST]/checkin/person.php?code=".base64_encode("ABC");
//         return (new QRCode)->render($link);
//     }
        
      
    }
}




