<?php
        session_start();
        require ("functions.php");
        checkLogin();
        checkArtist();
        $dbc = dbConn();
        $artistId = $_SESSION['user_Id'];
    
        //get all the artists
        $artistsResult = mysqli_query($dbc, "SELECT * FROM users as u
                                            INNER JOIN users_roles as ur ON u.user_Id = ur.user_Id
                                            INNER JOIN roles as r ON ur.role_Id = r.role_Id
                                            WHERE r.role = 'artist'");
        //get songwriters
        $getSongwriters = "SELECT * FROM users as u
        INNER JOIN users_roles as ur ON u.user_Id = ur.user_Id
        INNER JOIN roles as r ON ur.role_Id = r.role_Id
        WHERE r.role = 'songwriter'";
        $songwriterResult = mysqli_query($dbc, $getSongwriters);

        if(isset($_REQUEST['id'])){
            $songId = $_REQUEST['id'];
            $_SESSION['updateSong'] = $songId;


            $songResult = mysqli_query($dbc, "SELECT s.title, s.price, a.album_Id FROM songs s 
                                                JOIN artists_songs asg on s.song_Id = asg.song_Id 
                                                JOIN albums a ON s.album_Id = a.album_Id
                                                WHERE s.song_Id = $songId AND asg.user_Id = $artistId");
        $song = mysqli_fetch_array($songResult);
        $songtitle = $song[0];
        $songprice = $song[1];
        $albumId = $song[2];
        }
        if($_SERVER['REQUEST_METHOD'] == "POST"){
            $songId = $_SESSION['updateSong'];
            $songtitle = $_POST['songtitle'];
            $songprice = $_POST['price'];
            $albumId = $_POST['selectAlbum'];
            if(!empty($songtitle) && !empty($songprice) && !empty($albumId)){
                if(!empty($_FILES['songCover']['name'])){
                    $filename = $_FILES['songCover']['name'];
                    $tmpName = $_FILES['songCover']['tmp_name'];
                    $filepath = "images/" . $filename;
                    $updateSong = "UPDATE songs s 
                    SET s.title = '$songtitle', s.album_Id = $albumId, s.price = $songprice, 
                    s.photoSrc = '$filepath'
                    WHERE s.song_Id = $songId";
                }else{
                    $updateSong = "UPDATE songs s 
                    SET s.title = '$songtitle', s.album_Id = $albumId, s.price = $songprice
                    WHERE s.song_Id = $songId";
                }
    
                if($updateSongResult = mysqli_query($dbc, $updateSong)){
                    if(!empty($_POST['artists'])){
    
                        $collaborations = $_POST['artists'];

                        //check if the posted collaborators match with the ones in the table
                        foreach($collaborations as $ft){
                            $albumSongsResult = mysqli_query($dbc, "SELECT *  FROM artists_songs asg
                                            WHERE asg.song_Id = $songId AND asg.user_Id = $ft");
                            if(mysqli_num_rows($albumSongsResult) == 0){

                                //if the collab dont match than we need to insert it
                                insertArtistsSongWriterSongs($dbc, 'artists_songs',$songId, $ft);
                            }
                        }
                        }//end of foreach
                    }
                    if(!empty($_POST['songwriters'])){
                        $songwriters = $_POST['songwriters'];
                            
                    foreach($songwriters as $sw){
                            $songSongwriterResult = mysqli_query($dbc, "SELECT *  FROM songs_songwriter ss
                            WHERE ss.song_Id = $songId AND ss.user_Id = $sw");
                        if(mysqli_num_rows($songSongwriterResult) == 0){

                             //if the collab dont match than we need to insert it
                            mysqli_query($dbc, "INSERT INTO songs_songwriter(`song_Id`, `user_Id`) 
                            VALUES ($songId,$sw)");
                        }
                    }//end of foreach

                }
                $songUpdated = "Song was updated";
        }else{
            $required = '*';
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Song</title>
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
<div class="d-flex min-vh-100 justify-content-center align-items-center">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
    enctype="multipart/form-data"
    class="pe-2 ps-2 col-md-4 d-flex flex-column justify-content-evenly">
    <?php if(isset($required)){echo "<span class = \"text-danger align-self-center\">Please fill all the required fields</span>";} ?>
    <?php if(isset($songUpdated)){echo "<span class = \"text-success align-self-center\">$songUpdated</span>";} ?>
    <?php if(isset($albumNotFound)){echo "<span class = \"text-warning align-self-center\">$albumNotFound</span>";} ?>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="songtitle" class="form-label align-self-center mb-0 me-auto">Song Title:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($songtitle) && !isset($songUpdated) && !isset($albumNotFound))
             {echo $songtitle;} ?>"
            name="songtitle">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
            <div class="d-flex justify-content-evenly mb-3">
                <label for="selectAlbum" class="form-label align-self-center mb-0 me-auto ">User Studio</label>
                <select name="selectAlbum" class="form-select w-50 ps-4 pe-5">
                    <?php
                        $artsistAlbumsResult = mysqli_query($dbc,"SELECT a.album_Id, a.title FROM albums a 
                        JOIN artists_albums aa ON a.album_Id = aa.album_Id
                        WHERE aa.user_Id = $artistId");
                        while($artistAlbum = mysqli_fetch_array($artsistAlbumsResult)){
                            echo "<option value = \"$artistAlbum[0]\""; if($albumId ==$artistAlbum[0]){echo "selected";}
                            echo ">". strtoupper($artistAlbum[1])."</option>";
                        }
                    ?>
                </select>
                <div>
                        <?php if(isset($required)){echo "<span class = \"align-middle text-white\">$required</span>";}?>
                </div>
            </div>
        <div class="d-flex flex-column justify-content-center">
                    <div>Collabration with:</div>
                    <div class="w-100 flex-wrap"> 
                        <?php 
                            while($artist = mysqli_fetch_array($artistsResult)){
                                echo "<div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" name = \"artists[]\" type=\"checkbox\" value=\"$artist[0]\"
                                 id=\"$artist[0]\">
                                <label class=\"form-check-label\" for=\"$artist[0]\">
                                  $artist[1] $artist[2]
                                </label>
                              </div>";
                            }
                        ?>
                    </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="price" class="form-label align-self-center mb-0 me-auto">Price:</label>
            <input class="form-control w-50" type="number"
            placeholder="0.00" required name="price" min="0" step="0.01" title="Currency" pattern="^\d+(?:\.\d{1,2})?$"
            value="<?php if(isset($songprice) && !isset($songUpdated) && !isset($albumNotFound))
             {echo $songprice;} ?>"
            name="price">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="songCover" class="form-label align-self-center mb-0 me-auto">Song Cover Photo:</label>
            <input type="file" class="form-control w-50" name="songCover">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
       </div>

       <div class="d-flex flex-column justify-content-center">
                    <div>Songwriter/s:</div>
                    <div class="w-100 flex-wrap"> 
                        <?php 
                            while($songwriter = mysqli_fetch_array($songwriterResult)){
                                echo "<div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" name = \"songwriters[]\" 
                                type=\"checkbox\" value=\"$songwriter[0]\"
                                 id=\"$songwriter[0]\">
                                <label class=\"form-check-label\" for=\"$songwriter[0]\">
                                  $songwriter[1] $songwriter[2]
                                </label>
                              </div>";
                            }
                        ?>
                    </div>
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        

       <div class="d-flex justify-content-center align-items-center mb-3">
        <input type="submit" class="btn-primary rounded" id="signUp" value="Update Song">
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>