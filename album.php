<?php
    session_start();
    require ("functions.php");
    checkLogin();
    $userId = $_SESSION['user_Id'];
    $dbc = dbConn();
    if(isset($_REQUEST['id'])){
        $albumId = $_REQUEST['id'];

        //set the album id in the session so we can use it when comments are added with POST
        $_SESSION['updateAlbum'] = $albumId;

        //get the album info
        $albumsResult = mysqli_query($dbc, "SELECT * from albums a where a.album_Id = $albumId");
        $album = mysqli_fetch_array($albumsResult);

        //get the album songs
        $albumSongs = mysqli_query($dbc, "SELECT * FROM songs where album_Id = $albumId");

        //get comments
        $commentsResult = mysqli_query($dbc, "SELECT c.comment_Id, c.comment, u.first_name, u.last_name,
         u.user_Id FROM comments c 
        JOIN users u ON c.user_Id = u.user_Id
        JOIN albums a ON c.album_Id = a.album_Id
        WHERE a.album_Id = $albumId 
        ORDER BY c.comment_Id DESC LIMIT 8");

        //avg rate
        $rateResult = mysqli_query($dbc, "SELECT AVG(r.rate) FROM rates r
        JOIN albums a ON r.album_Id = a.album_Id
        WHERE a.album_Id = $albumId");
        $rate =round(mysqli_fetch_array($rateResult)[0], 1);
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        // get the album id from the session
        $albumId = $_SESSION['updateAlbum'];

        //check if the comment and rate are empty
        if(empty($_POST['comment']) && empty($_POST['rate'])){
            redirect("http://localhost/Music%20Studio%20System/album.php/?id=$albumId", 303);
        }

        //check if the comment is empty
        if(!empty($_POST['comment'])){
            $userComment = $_POST['comment'];
            $insertComment = mysqli_query($dbc, "INSERT INTO comments(user_Id, comment, album_Id)
                                 VALUES ($userId,'$userComment',$albumId)");

        }

        //check if the rate is empty
        if(!empty($_POST['rate'])){
            $rate = $_POST['rate'];
            $insertRates = mysqli_query($dbc, "INSERT INTO `rates`(`user_Id`, `rate`, `album_Id`)
                                                VALUES ($userId,$rate,$albumId)"); 
        }

        //refresh the page so we can show tho comments added
        redirect("http://localhost/Music%20Studio%20System/album.php/?id=$albumId", 303);

        
    }    
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $album[1]; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.4.1/font/bootstrap-icons.css">
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
            <a class="nav-link" href="http://localhost/Music%20Studio%20System/logout.php">Log out</a> 
        </div>
    </nav>
<div class="container d-flex p-2">
    <div class="col-4 text-center">
        <img class="img-fluid rounded-pill" src="<?php echo "http://localhost/Music%20Studio%20System/$album[4]";?>" alt="">
        <span class="fw-bolder fs-6 d-block"><?php echo $album[2];?>$</span>
        <p class="text-uppercase fw-lighter fs-4 lh-base"> 
            <?php
                 $getAlbumQuery = "SELECT u.first_name, u.last_name  FROM albums a 
                 JOIN artists_albums aa ON a.album_Id = aa.album_Id
                 JOIN users u ON aa.user_Id = u.user_Id
                 WHERE a.album_Id = $album[0]";
                 $albumCreators = mysqli_query($dbc, $getAlbumQuery);
                 while($albumCreator = mysqli_fetch_array($albumCreators)){
                     echo "$albumCreator[0] $albumCreator[1] <br>";
                 }
            ?>
            <span class="d-block text-capitalize fs-6">Audience Rating:<?php echo " <b>$rate/5</b>"?></span>
        </p>
    </div>
    <div class="col-8 text-center">
        <h3 class="fw-bold"><?php echo $album[1]?> <i class="bi bi-play-circle-fill text-info"></i> </h3>
        <div class="d-flex flex-column">
        <div class="d-flex justify-content-between">
                 <div class = 'col-4'>Song</div>
                 <div class = 'col-4'>Artists</div>
                 <div class = 'col-4'>Written By</div>
        </div>
                 <?php 
                 $i = 0;
                 while($song = mysqli_fetch_array($albumSongs)){
                     $i++;
                     echo "<div class = 'd-flex align-items-center justify-content-between fs-6 col-12 text-secondary border-1 border-bottom'>
                     <div class = 'col-4 text-start'>
                     <img class = 'w-25 rounded-pill' src = 'http://localhost/Music%20Studio%20System/$song[4]'>
                        $i. $song[1]
                     </div>
                     <div class = 'col-4'>"; 
                     $artistResult = mysqli_query($dbc, "SELECT u.first_name, u.last_name  FROM songs s
                     JOIN artists_songs asg ON s.song_Id = asg.song_Id
                     JOIN users u ON asg.user_Id = u.user_Id
                     WHERE s.song_Id = $song[0]");
                     while($artist = mysqli_fetch_array($artistResult)){
                         echo "$artist[0] $artist[1]<br>";
                     }
                     echo"</div>
                        <div class = 'col-4'>";
                        $songwriterResult = mysqli_query($dbc, "SELECT u.first_name, u.last_name  FROM songs s
                        JOIN songs_songwriter ss ON s.song_Id = ss.song_Id
                        JOIN users u ON ss.user_Id = u.user_Id
                        WHERE s.song_Id = $song[0]");
                        while($songwriter = mysqli_fetch_array($songwriterResult)){
                            echo "$songwriter[0] $songwriter[1]<br>";
                        }
                        echo"</div>
                     </div>";
                 }
                     ?>
        </div>
        <div class="d-flex flex-column justify-content-between">
        <div>
                 <h4 class="col-12 fs-5 text-secondary">Comments</h4>
                 <div class="col-12 p-2">
                    <?php 
                        while($comment = mysqli_fetch_array($commentsResult)){
                            echo "<div class = 'col-12 bg-light mt-1'>
                                <div class = 'col-4 text-info fw-bolder fs-6'>
                                $comment[2] $comment[3]
                                </div>
                                <div class = 'col-10 bg-light m-auto fw-lighter text-start fs-6 mb-1'>
                                $comment[1]
                                </div>";
                                if($comment[4] == $userId){
                                    echo "<div class = 'col-3 fs-6'>
                                    <a class = 'link-danger' href = 'http://localhost/Music%20Studio%20System/deleteComment.php/?id=$comment[0]'>
                                    Delete</a>
                                    </div>";
                                }
                            echo "</div>";
                        }
                    ?>
                 </div>
        </div>
        <form class="d-flex justify-content-evenly mt-4" method="POST"
         action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <input class="form-control w-auto me-2" name="comment" type="text" placeholder="Add a comment">
                <div class="col-4">
                <label class="form-label w-75 d-inline" for="rate">Rate the album:</label>
                <select class="form-select w-25 d-inline" name="rate" id="rate">
                <option value="0">  </option>
                 <option value="1">1</option>
                 <option value="2">2</option>
                 <option value="3">3</option>
                 <option value="4">4</option>
                 <option value="5">5</option>
                </select>
                </div>
                    <button class="btn btn-outline-primary" type="submit">Add</button>
        </form>
        </div>
    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>