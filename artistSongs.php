<?php
    session_start();
    require("functions.php");
    checkLogin();
    checkArtist();
    $dbc = dbConn();

    //get the user id from the session and get the songs from the db
    $artistId = $_SESSION['user_Id'];
    $getArtistSongsQuery = "SELECT s.song_Id, s.title, s.album_Id, s.price FROM songs s 
                        JOIN artists_songs as asg ON s.song_Id = asg.song_Id
                        JOIN users u ON asg.user_Id = u.user_Id
                        WHERE u.user_Id = $artistId";
    $artistSongs = mysqli_query($dbc, $getArtistSongsQuery);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Songs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

</head>
<body>
<nav class="navbar navbar-expand-sm navbar-light bg-light">
        <div class="container-fluid d-flex justify-content-between">
        <a class="navbar-brand" href="index.php">MusicMania</a>
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
<div class="container">
<table class="table table-dark table-striped table-hover mt-4">
                <thead>
                    <tr>
                    <th scope="col" class="text-center" >Title</th>
                    <th scope="col" class="text-center">Album</th>
                    <th scope="col" class="text-center">Features</th>
                    <th scope="col" class="text-center">Price</th>
                    <th scope="col" class="text-center"><a name = "addSong" href="addSong.php" class="btn btn-primary" role="button">Add Song</a></th>
                    </tr>
                </thead>
                <tbody>
                <?php while($song = mysqli_fetch_array($artistSongs)){
                    if($albumResult = mysqli_query($dbc, "SELECT a.title FROM albums a, songs s 
                                                WHERE s.album_Id = a.album_Id AND s.album_Id = $song[2]")){
                        $album = mysqli_fetch_array($albumResult);
                        $albumTitle = $album[0];
                    }
                    $getFeaturesResult = mysqli_query($dbc,"SELECT u.user_Id, u.first_name, u.last_name  FROM users u
                                                            JOIN artists_songs asg ON u.user_Id = asg.user_Id
                                                            JOIN songs s ON asg.song_Id = s.song_Id
                                                            WHERE s.song_Id = $song[0]");
                                            
                        echo "<tr>
                                <td class=\"text-center\" >$song[1]</td>
                                <td class=\"text-center\" >$albumTitle</td>
                                <td class=\"text-center\">";
                                while($feature = mysqli_fetch_array($getFeaturesResult)){
                                    if($feature[0] != $artistId){
                                        echo "$feature[1] $feature[2] <br>";
                                    }
                                }
                                echo "</td>
                                <td class=\"text-center\">$song[3]$</td>
                                <td class = \"text-center\">
                                <a name = \"delete\" href=\"deleteSong.php/?id=$song[0]\" 
                                class=\"btn btn-danger\" role=\"button\">Delete</a>
                                <a name = \"update\" href=\"updateSong.php/?id=$song[0]\" 
                                class=\"btn btn-success\" role=\"button\">Update</a>
                                </td>
                            </tr>";
                        }
                ?>
                </tbody>
</table>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>