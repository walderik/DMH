<?php
include_once 'db.inc.php';

// Now we check if the data was submitted, isset() function will check if the data exists.
if (!isset($_POST['name'], $_POST['password'], $_POST['email'])) {
    // Could not get the data that should have been sent.
    exit('Var vänlig fyll i alla fälten!');
}
// Make sure the submitted registration values are not empty.
if (empty($_POST['name']) || empty($_POST['password']) || empty($_POST['email'])) {
    // One or more values are empty.
    exit('Var vänlig fyll i alla fälten!');
}

if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    exit('Eposten är inte korrekt!');
}

if (strlen($_POST['password']) > 20 || strlen($_POST['password']) < 5) {
    exit('Lösenordet måste vara mellan 5 och 20 tecken långt!');
}

// We need to check if a user with that email exists.
if ($stmt = $conn->prepare('SELECT id, password FROM users WHERE email = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), hash the password using the PHP password_hash function.
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    $stmt->store_result();
    // Store the result so we can check if the account exists in the database.
    if ($stmt->num_rows > 0) {
        // Email already exists
        echo 'Du är redan registrerad.!';
    } else {
        // User doesnt exists, insert new account
        if ($stmt = $conn->prepare('INSERT INTO users (Name, Password, Email, ActivationCode) VALUES (?, ?, ?, ?)')) {            // We do not want to expose passwords in our database, so hash the password and use password_verify when a user logs in.
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $uniqid = uniqid();
            
            //TODO bygg aktivering av konto. Kräver mail.
            $uniqid = 'activated';
            $stmt->bind_param('ssss', $_POST['name'], $password, $_POST['email'], $uniqid);
            $stmt->execute();
            
            /*
            $from    = 'noreply@yourdomain.com';
            $subject = 'Aktivera konto';
            $headers = 'From: ' . $from . "\r\n" . 'Reply-To: ' . $from . "\r\n" . 'X-Mailer: PHP/' . phpversion() . "\r\n" . 'MIME-Version: 1.0' . "\r\n" . 'Content-Type: text/html; charset=UTF-8' . "\r\n";
            // Update the activation variable below
            $activate_link = 'http://yourdomain.com/phplogin/activate.php?email=' . $_POST['email'] . '&code=' . $uniqid;
            $message = '<p>Please click the following link to activate your account: <a href="' . $activate_link . '">' . $activate_link . '</a></p>';
            mail($_POST['email'], $subject, $message, $headers);
            */
            echo 'Please check your email to activate your account!';
        } else {
            // Something is wrong with the sql statement, check to make sure users table exists with all 3 fields.
            echo 'Could not prepare statement!';
        }}
    $stmt->close();
} else {
    // Something is wrong with the sql statement, check to make sure users table exists with all 3 fields.
    echo 'Could not prepare statement!';
}
$conn->close();
