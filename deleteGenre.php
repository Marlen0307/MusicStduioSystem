<?php
session_start();
require("functions.php");
checkLogin();
checkAdmin();
$dbc = dbConn();
if(isset($_REQUEST['id']) && isset($_REQUEST['genreId'])){
    $genreId = $_REQUEST['genreId'];
    $studioId = $_REQUEST['id'];
    echo $genreId . $studioId;
    $result = mysqli_query($dbc, "DELETE FROM studio_genres 
                                  WHERE genre_Id = $genreId AND studio_Id = $studioId");
    if($result){
        mysqli_close($dbc);
        redirect('http://localhost/Music%20Studio%20System/studioGenres.php/', 303);
    }
}

?>