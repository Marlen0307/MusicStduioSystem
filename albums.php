<?php
    session_start();
    require ("functions.php");
    checkLogin();
    $dbc = dbConn();
    $albumsResult = mysqli_query($dbc, "SELECT * from albums");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Albums</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body>
<nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">
        <a class="navbar-brand" href="http://localhost/Music%20Studio%20System/index.php">MusicMania</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse justify-content-center text-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/Music%20Studio%20System/albums.php">
                        Albums</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="http://localhost/Music%20Studio%20System/systemStudios.php">
                        Studios</a>
                    </li>
                </ul>
                <?php if(isset($_SESSION['role'])&& $_SESSION['role'] == 'admin')
                    {echo"<a href =\"addUser.php\" class = \"nav-link\">Add User</a>";
                    echo "<a href =\"Users.php\" class = \"nav-link\">Users</a>";
                    echo "<a href =\"addStudio.php\" class = \"nav-link\">Add Studio</a>";
                    echo "<a href=\"Studios.php\" class=\"nav-link\">Studios</a>";
                    }if(isset($_SESSION['role']) && $_SESSION['role'] == 'artist'){
                        echo "<a href =\"http://localhost/Music%20Studio%20System/artistSongs.php\" class = \"nav-link\">Your Songs</a>";
                        echo "<a href =\"http://localhost/Music%20Studio%20System/artistAlbum.php\" class = \"nav-link\">Your Albums</a>";;
                    } ?>
            </div>
            <a class="nav-link" href="logout.php">Log out</a> 
        </div>
    </nav>
<div class="d-flex flex-wrap container justify-content-between">
     <?php 
        while($album = mysqli_fetch_array($albumsResult)){
            echo "<div class = \" col-3 m-2  text-center border p-2 \"> 
            <img class =\"w-75 rounded-pill\" src = \"$album[4]\">
            <h3><a href = \"album.php/?id=$album[0]\" class=\"link-dark text-decoration-none\">$album[1]</a><h3>
            <p>"; 
            $getAlbumQuery = "SELECT u.first_name, u.last_name  FROM albums a 
            JOIN artists_albums aa ON a.album_Id = aa.album_Id
            JOIN users u ON aa.user_Id = u.user_Id
            WHERE a.album_Id = $album[0]";
            $albumCreators = mysqli_query($dbc, $getAlbumQuery);
            while($albumCreator = mysqli_fetch_array($albumCreators)){
                echo "<span class = 'fs-5 fw-lighter fst-italic'>$albumCreator[0] $albumCreator[1]</span> <br>";
            }
            echo "</p> <p class = 'fs-4 text-secondary'>Studios <br>";
            $albumStduio = "SELECT s.name from albums a 
            JOIN studios_albums sa ON a.album_Id = sa.album_Id
            JOIN studios s ON s.studio_Id = sa.studio_Id
            WHERE a.album_Id = $album[0]";
            $albumStudioResult = mysqli_query($dbc, $albumStduio);
            while($studio = mysqli_fetch_array($albumStudioResult)){
                echo "<span class = 'fw-bolder fst-italic fs-4 text-dark'> $studio[0] </span> <br>";
            }
            echo"</p></div>";
        }
     ?>
</div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>