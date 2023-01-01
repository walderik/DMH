<?php
include_once 'db.inc.php';

session_start();

// Now we check if the data from the login form was submitted, isset() will check if the data exists.
if ( !isset($_POST['email'], $_POST['password']) ) {
    // Could not get the data that should have been sent.
    exit('Fyll i både epost och lösenord!');
}

// Prepare our SQL, preparing the SQL statement will prevent SQL injection.
if ($stmt = $conn->prepare('SELECT id, password, ActivationCode, IsAdmin FROM users WHERE email = ?')) {
    // Bind parameters (s = string, i = int, b = blob, etc), in our case the username is a string so we use "s"
    $stmt->bind_param('s', $_POST['email']);
    $stmt->execute();
    // Store the result so we can check if the account exists in the database.
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $password, $activation_code, $isAdmin);
        $stmt->fetch();
        // Account exists, now we verify the password.
        // Note: remember to use password_hash in your registration file to store the hashed passwords.
        if (password_verify($_POST['password'], $password)) {
            // Verification success! User has given correct credentials!
            //Check that the user is activated
            if ($activation_code == 'activated') {
                // account is activated
                // Create sessions, so we know the user is logged in, they basically act like cookies but remember the data on the server.
                session_regenerate_id();
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                if ($isAdmin == 1) { $_SESSION['admin'] = true;
                header('Location: ../participant/index.php');
            } else {
                echo 'Kontot är inte aktiverat!';
            }                       
        } else {
            // Incorrect password
            echo 'Felaktig epost eller lösenord!';
        }
        
    } else {
        // Incorrect email
        echo 'Felaktig epost eller lösenord!';
    }
 
    $stmt->close();
}