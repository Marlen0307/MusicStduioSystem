<?php
    session_start();
    require ("functions.php");
    checkLogin();
    $dbc = dbConn();
    if(isset($_REQUEST['id'])){
        $studioId = $_REQUEST['id'];
       $studioResult =  mysqli_query($dbc, "SELECT * from studios where studio_Id = $studioId");
       $studio = mysqli_fetch_array($studioResult);
    $studioGenres = mysqli_query($dbc, "SELECT g.genre_Id, g.genre FROM studio_genres sg
    JOIN genres g ON sg.genre_Id = g.genre_Id
    WHERE sg.studio_Id = $studioId");
    $studioPeople = mysqli_query($dbc, "SELECT u.first_name, u.last_name FROM users u 
    JOIN users_studio us ON u.user_Id = us.user_Id
    WHERE us.studio_Id = $studioId limit 10");
    $studioAlbums = mysqli_query($dbc, "SELECT a.title FROM albums a 
    JOIN studios_albums sa ON a.album_Id = sa.album_Id
    WHERE sa.studio_Id = $studioId limit 10");

    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $studio[1]?></title>
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
    <div class="container d-flex text-center p-3">
        <div class="col-4">
            <img src="<?php echo "http://localhost/Music%20Studio%20System/$studio[6]"?>" class="img-fluid rounded-pill">
            <div>
                <p class=""> 
                    <h3 class="fw-bolder fs-4 lh-lg">
                        <?php echo "$studio[1]";?>
                    </h3>
                    <span class="text-muted d-block lh-lg fs-5">
                        <?php echo "$studio[2]";?>
                    </span>
                    <span class="font-monospace d-block lh-lg fs-5">
                        <?php echo "<b>Email:</b>$studio[4]<br>
                                    <b>Mobile:</b>$studio[3]";?>
                    </span>
                    <span class="font-monospace d-block lh-lg fs-5">
                        <?php echo "<b>Founded on:</b>$studio[5]";?>
                    </span>
                </p>
            </div>
        </div>
        <div class="col-8 d-flex flex-column">
            <div class="col-12">
                <h4 class="text-secondary fs-5 border-1 border-bottom">Genres</h4>
                <div class="flex-wrap w-25 m-auto text-muted">
                    <?php 
                        $i = 0;
                        while($genre = mysqli_fetch_array($studioGenres)){
                            $i++;
                            echo "$i. $genre[1]"; echo str_repeat('&nbsp;', 5);
                        }
                    ?>
                </div>
            </div>
            <div class="col-12">
                <h4 class="text-secondary fs-5 border-1 border-bottom justify-content-between">People</h4>
                <div class="flex-wrap w-25 m-auto text-muted">
                    <?php 
                        $i = 0;
                        while($worker = mysqli_fetch_array($studioPeople)){
                            $i++;
                            echo "$i. $worker[0] $worker[1]"; echo str_repeat('&nbsp;', 5);
                        }
                    ?>
                </div>
            </div>
            <div class="col-12">
                <h4 class="text-secondary fs-5 border-1 border-bottom">Albums</h4>
                <div class="flex-wrap w-25 m-auto text-muted">
                    <?php 
                    $i = 0;
                        while($album = mysqli_fetch_array($studioAlbums)){
                            $i++;
                            echo "$i. $album[0]"; echo str_repeat('&nbsp;', 5);
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>