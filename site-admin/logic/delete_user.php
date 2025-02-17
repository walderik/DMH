<?php
include_once '../header.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        User::delete($id);
        header('Location: ../user_unused.php?message=user_deleted');
        exit;
    }
}

header('Location: ../index.php');
exit;

