<?php
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $db_name = "gradebook_database"; 
    $conn = new mysqli($servername, $db_username, $db_password, $db_name); //create initial connection

    //test connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
?>
