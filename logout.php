<?php
    session_start(); // start the session
    session_unset();
    session_destroy(); 
    header('Location: index.php');
    exit(); 