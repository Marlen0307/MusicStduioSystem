<?php
    session_start();
    require ("functions.php");
    checkLogin();
    checkArtist();
    $dbc = dbConn();

    //check if the id is passed
    if(isset($_REQUEST['id'])){
        $albumId = $_REQUEST['id'];
        delete('album_Id',$albumId, 'studios_albums', $dbc);
        delete('album_Id',$albumId, 'comments', $dbc);
        delete('album_Id',$albumId, 'rates', $dbc);
        delete('album_Id',$albumId, 'artists_albums', $dbc);
        mysqli_query($dbc, "DELETE asg FROM artists_songs asg
                            JOIN songs s ON asg.song_Id = s.song_Id
                            WHERE s.album_Id = $albumId");
        mysqli_query($dbc, "DELETE ss FROM songs_songwriter ss
                            JOIN songs s ON ss.song_Id = s.song_Id
                            WHERE s.album_Id = $albumId");;
         delete('album_Id',$albumId, 'songs', $dbc);
         if($result = delete('album_Id',$albumId, 'albums', $dbc)){
             mysqli_close($dbc);
             redirect('http://localhost/Music%20Studio%20System/artistAlbum.php', 303);
         }

    }
?>