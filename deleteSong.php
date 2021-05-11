<?php
    session_start();
    require ("functions.php");
    checkLogin();
    checkArtist();
    if($_REQUEST['id']){
        $dbc = dbConn();
        $songId = $_REQUEST['id'];
        delete('song_Id', $songId, 'songs_songwriter', $dbc);
        delete('song_Id', $songId, 'artists_songs', $dbc);
        if($result = delete('song_Id', $songId, 'songs', $dbc)){
            redirect('http://localhost/Music%20Studio%20System/artistSongs.php', 303);
        }     
    }
?>