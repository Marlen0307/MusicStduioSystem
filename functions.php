<?php
    //file for writing global functions

   //db connection
    function dbConn(){
      $dbc =  mysqli_connect('localhost', 'marlen', 'marlen1234', 'music_studio_system')
         or die('Couldn\'t connect to database');
         return $dbc;
    }
     //function to redirect the user
     function redirect($url, $statusCode = 303){
        header('Location: ' . $url, true, $statusCode);
        die();
    }

     //function to check if user is admin and is loged in
     function checkAdmin(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
           redirect("http://localhost/Music%20Studio%20System/login.php", 303);
        }
    }
    function checkArtist(){
        if(!isset($_SESSION['role']) || $_SESSION['role'] != 'artist'){
           redirect("http://localhost/Music%20Studio%20System/login.php", 303);
        }
    }
    function checkLogin(){
        if(!isset($_SESSION['user_Id'])){
            redirect("http://localhost/Music%20Studio%20System/login.php", 303);
        }
    }

     //function for checking if the data supplied is in the users table
    function checkUser($email){
        $selectUser= "SELECT * FROM users WHERE email = '$email'";
        $resultSelect =  mysqli_query(dbConn(), $selectUser);
        return $userData = mysqli_fetch_array($resultSelect);
    }

    //function to check if a user has a certain role in the system
    function checkUserandRole($userId, $role){
        $selectUserAndRole = "SELECT * FROM users as u
                            INNER JOIN users_roles as ur ON u.user_Id = ur.user_Id
                            INNER JOIN roles as r ON ur.role_Id = r.role_Id
                            WHERE u.user_Id = $userId and r.role_Id = $role";
        $result = mysqli_query(dbConn(), $selectUserAndRole);
        if(mysqli_num_rows($result)>0){
            return true;
        }
    }

    //funtion to delete user from all tables related to him
    function delete($field, $Id, $table, $dbc){
        $deleteQuery = "DELETE FROM $table WHERE $field = $Id ";
        $result=mysqli_query($dbc, $deleteQuery) or die("Cant delete row!");
        return $result;
    }

    //function to insert into studio_genres table
    function insertIntoStudioGenres($dbc, $studioId, $genreId){
        $query = "INSERT INTO studio_genres(studio_Id, genre_Id) VALUES ($studioId,$genreId)";
        mysqli_query($dbc, $query);
    }

    //function to get the studio
    function checkStudio($dbc, $email){
        $studioResult = mysqli_query($dbc, "SELECT * FROM studios WHERE studios.email = '$email'");
        if(mysqli_num_rows($studioResult)>0){
            return mysqli_fetch_array($studioResult);
        }else{
            return false;
        }
    }

    //insert into stuidos_albums
    function insertStudioAlbums($dbc, $albumId, $studioId){
        $query  ="INSERT INTO studios_albums(album_Id, studio_Id)
                        VALUES ($albumId, $studioId)";
    mysqli_query($dbc,$query) 
                        or die("Cant insert into studios_albums");
    }

    //inser into artists_albums
    function insertArtistsAlbums ($dbc, $albumId, $ft){
        $insertCollaborations = "INSERT INTO artists_albums(user_Id, album_Id)
                                VALUES ($ft, $albumId)";
                                mysqli_query($dbc, $insertCollaborations);
    }

    //insert into artists_songs
    function insertArtistsSongWriterSongs($dbc,$table,  $songId, $ft){
        $query = "INSERT INTO $table(song_Id, user_Id) VALUES ($songId,$ft)";
        mysqli_query($dbc, $query);
    }


    
    
?>