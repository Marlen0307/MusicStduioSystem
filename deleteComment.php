<?php
    session_start();
    require ("functions.php");
    checkLogin();
    if($_REQUEST['id']){
        $dbc = dbConn();
        $albumId = $_SESSION['updateAlbum'];
        $commentid = $_REQUEST['id'];
        $deletecomment = mysqli_query($dbc, "DELETE FROM comments where comment_Id = $commentid");
        if($deletecomment){
            redirect("http://localhost/Music%20Studio%20System/album.php/?id=$albumId", 303);        
        }
    }
?>