<?php
    session_start();
    require("functions.php");
    checkLogin();
    checkAdmin();
    if($_REQUEST['id']){
        $studioId = $_REQUEST['id'];
         $dbc = dbConn();
         delete('studio_Id',$studioId, 'users_studio', $dbc);
         delete('studio_Id',$studioId, 'studios_albums', $dbc);
         delete('studio_Id',$studioId, 'studio_genres', $dbc);
         $result = delete('studio_Id',$studioId, 'studios', $dbc);
         if($result){
             mysqli_close($dbc);
             redirect("http://localhost/Music%20Studio%20System/Studios.php", 303);
         }
    }
?>