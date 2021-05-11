<?php
session_start();
require ("functions.php");
checkLogin();
if($_REQUEST['id']){
    $dbc = dbConn();
    $artistId = $_REQUEST['id'];
    $getArtistInfo = "SELECT u.user_Id, u.first_name, u.last_name, u.birthday, u.adress , u.photoSrc, s.name 
                        FROM users u 
                        JOIN users_studio us ON u.user_Id = us.user_Id
                        JOIN studios s ON us.studio_Id = s.studio_Id
                        WHERE U.user_Id = $artistId";
    $artistResult = mysqli_query($dbc, $getArtistInfo);
    $artist = mysqli_fetch_array($artistResult);
    $getRolesQuery = "SELECT r.role FROM roles r
                JOIN users_roles ur ON r.role_Id = ur.role_Id
                JOIN users u ON u.user_Id = ur.user_Id
                WHERE u.user_Id = $artistId";
    $getRolesResult = mysqli_query($dbc, $getRolesQuery);
    $getSongs = "SELECT s.song_Id, s.title, s.price FROM songs s
    JOIN artists_songs asg ON s.song_Id = asg.song_Id
    WHERE asg.user_Id = $artistId
    ORDER BY s.song_Id DESC LIMIT 5";
    $getSongsResult = mysqli_query($dbc, $getSongs);
    $getAlbums = "SELECT a.title FROM albums a
    JOIN artists_albums aa ON a.album_Id = aa.album_Id
    JOIN users u ON u.user_Id = aa.user_Id
    WHERE u.user_Id = $artistId LIMIT 5";
    $getAlbumsResult = mysqli_query($dbc, $getAlbums);

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artist</title>
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
                    {echo"<a href =\"http://localhost/Music%20Studio%20System/addUser.php\" class = \"nav-link\">Add User</a>";
                    echo "<a href =\"http://localhost/Music%20Studio%20System/Users.php\" class = \"nav-link\">Users</a>";
                    echo "<a href =\"addStudio.php\" class = \"nav-link\">Add Studio</a>";
                    echo "<a href=\"Studios.php\" class=\"nav-link\">Studios</a>";
                    }if(isset($_SESSION['role']) && $_SESSION['role'] == 'artist'){
                        echo "<a href =\"http://localhost/Music%20Studio%20System/artistSongs.php\" class = \"nav-link\">Your Songs</a>";
                        echo "<a href =\"http://localhost/Music%20Studio%20System/artistAlbum.php\" class = \"nav-link\">Your Albums</a>";;
                    } ?>
            </div>
            <a class="nav-link" href="http://localhost/Music%20Studio%20System/logout.php">Log out</a> 
        </div>
    </nav>
    <div class="container d-flex">
    <div class="col-5 text-center">
    <img class="img-fluid rounded-pill" src="<?php echo "http://localhost/Music%20Studio%20System/$artist[5]";?>" alt="">
    <p class="" ><?php while($role = mysqli_fetch_array($getRolesResult)){
        echo "<h3 class=\"fst-italic fw-lighter text-center\">$role[0]</h3>";
    } ?></p>
    </div>
    <div class="col-6 p-3">
        <div class="w-75 m-auto">
        <div class="col-12 text-center fw-bolder fs-4 fst-italic m-3"><?php echo $artist[1] . ' ' . $artist[2] ?></div>
        <div class="col-12 text-center fs-6 fst-italic m-3"><?php echo $artist[3]?></div>
        <div class="col-12 text-center fw-bolder fs-4 fst-italic m-3"><?php echo $artist[4]?></div>
        <div class="col-12 text-center fw-bolder fs-4 fst-italic m-3"><?php echo $artist[6]?></div>
        </div>
        <div class="col-12 mt-5">
        <h3 class="text-center text-secondary fw-bolder">Latest songs</h3>
            <?php $i = 0;
            while($song = mysqli_fetch_array($getSongsResult)){
                $i++;
                echo "<div class = 'text-secondary border-1 border-bottom fw-lighter col-12 fs-5 text-center m-2'>$i. $song[1]</div>";
            }
                ?>
        </div>
        <div class="col-12 mt-5">
        <h3 class="text-center text-secondary fw-bolder">Albums</h3>
            <?php 
            $i=0;
            while($album = mysqli_fetch_array($getAlbumsResult)){
                $i++;
                echo "<div class = 'text-secondary border-1 border-bottom 
                fw-lighter col-12 fs-5 text-center m-2'>$i. $album[0]</div>";
            }
                ?>
        </div>
    </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>