<?php

global $root, $current_user, $current_larp;
$root = $_SERVER['DOCUMENT_ROOT'];
require $root . '/includes/init.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //Hitta rätt user
    if (isset($_POST['UserEmail'])) {
        $user = User::loadByEmail($_POST['UserEmail']);
        if (isset($user)) {
            if ($user->Id == $current_person->UserId) {
                header('Location: ../index.php?error=same_user');
                exit;
            }
            $current_person->changeUser($user);
            $current_person = null;
            $_SESSION['PersonId'] = null;
            header('Location: ../index.php?message=user_changed');
            exit;
        }
    }
}

header('Location: ../index.php?error=user_missing');




