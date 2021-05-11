<?php
require ("functions.php");
session_start();

    //check if the user is loged in 
    checkLogin();

    //db connection
    $dbc = dbConn();

    $getArtists = "SELECT * FROM users INNER JOIN users_roles ON users.user_Id = users_roles.user_Id
                   INNER JOIN roles ON users_roles.role_Id = roles.role_Id WHERE roles.role = 'artist'";
    $result = mysqli_query($dbc, $getArtists);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicMania</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body class="bg-light">
    <nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">
        <a class="navbar-brand" href="#">MusicMania</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
        </button>
            <div class="collapse navbar-collapse justify-content-center text-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="albums.php">Albums</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="systemStudios.php">Studios</a>
                    </li>
                </ul>
                <?php if(isset($_SESSION['role'])&& $_SESSION['role'] == 'admin')
                    {echo"<a href =\"addUser.php\" class = \"nav-link\">Add User</a>";
                    echo "<a href =\"Users.php\" class = \"nav-link\">Users</a>";
                    echo "<a href =\"addStudio.php\" class = \"nav-link\">Add Studio</a>";
                    echo "<a href=\"Studios.php\" class=\"nav-link\">Studios</a>";
                    }if(isset($_SESSION['role']) && $_SESSION['role'] == 'artist'){
                        echo "<a href =\"artistSongs.php\" class = \"nav-link\">Your Songs</a>";
                        echo "<a href =\"artistAlbum.php\" class = \"nav-link\">Your Albums</a>";;
                    } ?>
            </div>
            <a class="nav-link" href="logout.php">Log out</a> 
        </div>
    </nav>
    <div class="container text-center">
    <h1 class="mt-3">Welcome to MusicMania!</h1>
    <h3 class="mt-3">Artists suscribed to our system:</h3>
    </div>
   <div class="d-flex flex-wrap container justify-content-between">
   <?php 
        while($artist = mysqli_fetch_array($result)){
            echo "<div class = \" col-3 m-3 text-center \"> 
            <img class =\"w-75 rounded-pill\" src = \"$artist[8]\">
            <h3><a href = \"artist.php/?id=$artist[0]\" class=\"link-dark text-decoration-none\">$artist[1]  $artist[2]</a><h3></div>";
        }
    ?>
   </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>