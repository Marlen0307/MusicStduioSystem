<?php 
    session_start();
    require ("functions.php");
    checkLogin();
    checkAdmin();
    if($_REQUEST['id']){
        $dbc = dbConn();
        $userId =  $_REQUEST['id'];
        delete('user_Id', $userId, 'users_roles', $dbc);
        delete('user_Id',$userId, 'users_studio', $dbc);
        delete('user_Id',$userId, 'songs_songwriter', $dbc);
        delete('user_Id',$userId, 'comments', $dbc);
        delete('user_Id',$userId, 'rates', $dbc);
        delete('user_Id',$userId, 'artists_songs', $dbc);
        delete('user_Id',$userId, 'artists_albums', $dbc);
        $result = delete('user_Id',$userId, 'users', $dbc);
        if($result){
            redirect('http://localhost/Music%20Studio%20System/Users.php', 303);
        }
    }
    
?>