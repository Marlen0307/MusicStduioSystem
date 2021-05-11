<?php
    session_start();
    require ("functions.php");
    checkArtist();
    checkLogin();
    $artistId = $_SESSION['user_Id'];
    $dbc = dbConn();
    $studiosResult = mysqli_query($dbc, "SELECT * FROM studios");
    $artistsResult = mysqli_query($dbc, "SELECT * FROM users as u
                                        INNER JOIN users_roles as ur ON u.user_Id = ur.user_Id
                                        INNER JOIN roles as r ON ur.role_Id = r.role_Id
                                        WHERE r.role = 'artist'");

    if($_SERVER['REQUEST_METHOD'] == "POST"){
        $albumtitle = $_POST['albumtitle'];
        $releasedate = $_POST['releasedate'];
        $albumprice = $_POST['price'];
        if(!empty($albumtitle) && !empty($releasedate) && $_FILES['albumCover']
         && $_POST['check_list']){
           
            $selectedStudios = $_POST['check_list'];

            //query to check if the album is in the db
            $getAlbum= "SELECT * from albums a 
            join studios_albums sa ON a.album_Id = sa.album_Id
            JOIN artists_albums aa ON aa.album_Id = a.album_Id
            WHERE a.title = '$albumtitle' AND aa.user_Id = $artistId";
            $albumExistsResult =  mysqli_query($dbc, $getAlbum);

            //check if the album already exists
            if(mysqli_num_rows($albumExistsResult) == 0){
            

                    // get the image path
                    $filename = $_FILES['albumCover']['name'];
                    $tmpName = $_FILES['albumCover']['tmp_name'];
                    $filepath = "images/" . $filename;
                    move_uploaded_file($tmpName, $filepath);


                    //insert into songs if the albumid is returned
                    $insertAlbumQuery = "INSERT INTO albums(title, price,release_date,photoSrc)
                                        VALUES ('$albumtitle',$albumprice,'$releasedate', '$filepath')";
                    $insertAlbumResult = mysqli_query($dbc, $insertAlbumQuery);
                    if($insertAlbumResult){

                        //get the newly registered album id if the album is inserted
                        $getAlbumIdQuery = "SELECT a.album_Id FROM albums a where a.title = '$albumtitle' 
                        AND a.price = $albumprice AND a.release_date = '$releasedate' 
                        AND a.photoSrc ='$filepath'";
                        $albumIdResult = mysqli_query($dbc, $getAlbumIdQuery) or die("Cant get studioId ");
                        $album = mysqli_fetch_array($albumIdResult);
                        $albumId = $album[0];
                        //insert into artists-albums
                        $insertArtistAlbums = "INSERT INTO artists_albums(user_Id, album_Id)
                        VALUES ($artistId, $albumId)";
                        $insertArtistAlbumsResult =mysqli_query($dbc,$insertArtistAlbums) 
                        or die("Cant insert into artists-albums");

                        //insert collaborations in db
                        if(!empty($_POST['artists'])){
                            $collaborations = $_POST['artists'];
                            foreach($collaborations as $ft){
                                insertArtistsAlbums($dbc, $albumId, $ft);
                            }
                        }

                        //insert into studios-album
                      foreach($selectedStudios as $sStudio){
                        insertStudioAlbums($dbc, $albumId, $sStudio);
                        $albumInserted = "Album registered successfuly";
                      }



                    }
            }else{
                $albumExists = "This album already exists in the system";
            }

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
    <title>Your albums</title>
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
<div class="d-flex min-vh-100 justify-content-center align-items-center">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
    enctype="multipart/form-data"
    class="pe-2 ps-2 col-md-4 d-flex flex-column justify-content-evenly">
    <?php if(isset($required)){echo "<span class = \"text-danger align-self-center\">Please fill all the required fields</span>";} ?>
    <?php if(isset($albumInserted)){echo "<span class = \"text-success align-self-center\">$albumInserted</span>";} ?>
    <?php if(isset($albumExists)){echo "<span class = \"text-warning align-self-center\">$albumExists</span>";} ?>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="albumtitle" class="form-label align-self-center mb-0 me-auto">Album Title:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($albumtitle) && !isset($albumInserted))
             {echo $albumtitle;} ?>"
            name="albumtitle">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
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
            placeholder="0.00" required name="price" min="0" step="0.5" title="Currency" pattern="^\d+(?:\.\d{1,2})?$"
            value="<?php if(isset($albumprice) && !isset($albumInserted))
             {echo $albumprice;} ?>"
            name="price">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <div class="d-flex flex-column justify-content-center">
                    <div>Studios:</div>
                    <div class="w-100 flex-wrap"> 
                        <?php 
                            while($studio = mysqli_fetch_array($studiosResult)){
                                echo "<div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" name = \"check_list[]\" type=\"checkbox\" value=\"$studio[0]\"
                                 id=\"$studio[1]\">
                                <label class=\"form-check-label\" for=\"$studio[1]\">
                                  $studio[1]
                                </label>
                              </div>";
                            }
                        ?>
                    </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3"> 
            <label for="releasedate" class="form-label align-self-center mb-0 me-auto">Release Date:</label>
            <input type="date" class="form-control w-50" 
            value="<?php if(isset($releasedate) && !isset($albumInserted)){echo $releasedate;} ?>"
            name="releasedate">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="albumCover" class="form-label align-self-center mb-0 me-auto">Album Cover Photo:</label>
            <input type="file" class="form-control w-50" name="albumCover">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
       </div>
        

       <div class="d-flex justify-content-center align-items-center mb-3">
        <input type="submit" class="btn-primary rounded" id="signUp" value="Add Album">
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>