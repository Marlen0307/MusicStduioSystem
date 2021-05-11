<?php   
    session_start();
    if(isset($_SESSION['user_Id'])){
        unset($_SESSION['user_Id']);
        header("Location: login.php");
    }
?>