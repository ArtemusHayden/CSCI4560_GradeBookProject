<?php
    session_start(); //destroy session data
    session_unset();        
    session_destroy();      

    header("Location: home.php"); //return to home
    exit;
?>
