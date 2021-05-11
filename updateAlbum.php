<?php
    session_start();
    require ("functions.php");
    checkLogin();
    checkArtist();
    $artistId = $_SESSION['user_Id'];
    $dbc = dbConn();

    //get the values for checklists
    $studiosResult = mysqli_query($dbc, "SELECT * FROM studios");
    $artistsResult = mysqli_query($dbc, "SELECT * FROM users as u
                                        INNER JOIN users_roles as ur ON u.user_Id = ur.user_Id
                                        INNER JOIN roles as r ON ur.role_Id = r.role_Id
                                        WHERE r.role = 'artist'");

    if(isset($_REQUEST['id'])){
        $albumId = $_REQUEST['id'];
        $_SESSION['updateAlbum'] = $albumId;

        //get the values of the album set in the db
        $getAlbumQuery = "SELECT a.album_Id, a.title, a.price, a.release_date
                    FROM albums a JOIN artists_albums aa ON a.album_Id = aa.album_Id
                    JOIN studios_albums sa ON sa.album_Id = a.album_Id
                    WHERE a.album_Id = $albumId";
        $albumResult = mysqli_query($dbc, $getAlbumQuery);
        $album = mysqli_fetch_array($albumResult);
        $albumtitle = $album[1];
        $albumprice = $album[2];
        $releasedate = $album[3];
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        //get the values when the form is posted
        $albumId = $_SESSION['updateAlbum'];
        $albumtitle = $_POST['albumtitle'];
        $releasedate = $_POST['releasedate'];
        $albumprice = $_POST['price'];

        //check if the values are empty
        if(!empty($albumtitle) && !empty($releasedate) && !empty($albumprice)){

            //check if there is any update in the logo
                if($_FILES['albumCover']){
                    $filename = $_FILES['albumCover']['name'];
                    $tmpName = $_FILES['albumCover']['tmp_name'];
                    $filepath = "images/" . $filename;
                    $updateAlbum = "UPDATE albums a
                                    SET a.title = '$albumtitle', a.price = $albumprice, 
                                    a.release_date = '$releasedate', a.photoSrc = '$filepath'
                                    WHERE a.album_Id = $albumId";
                }else{

                    //else we will not involve filepath in the query
                    $updateAlbum = "UPDATE albums a
                                    SET a.title = '$albumtitle', a.price = $albumprice, 
                                    a.release_date = '$releasedate'
                                    WHERE a.album_Id = $albumId";
                }
               if($updateAlbumResult =  mysqli_query($dbc, $updateAlbum)){

                    //check if there are any collaborators
                    if(!empty($_POST['artists'])){

                        $collaborations = $_POST['artists'];

                        //check if the posted collaborators match with the ones in the table
                        foreach($collaborations as $ft){
                            $albumArtistResult = mysqli_query($dbc, "SELECT *  FROM artists_albums aa
                                            WHERE aa.album_Id = $albumId AND aa.user_Id = $ft");
                            if(mysqli_num_rows($albumArtistResult) == 0){

                                //if the collab dont match than we need to insert it
                                insertArtistsAlbums($dbc, $albumId, $ft);
                            }
                        }//end of foreach
                    }

                    // same thing with the studios
                    if(!empty($_POST['check_list'])){
                        $albumstudio = $_POST['check_list'];
                        foreach($albumstudio as $aStudio){
                            if($albumStudioResult = mysqli_query($dbc, "SELECT *  
                                                            FROM studios_albums sa
                                                             WHERE sa.album_Id = $albumId
                                                              AND sa.studio_Id = $aStudio")){
                                                
                            if(mysqli_num_rows($albumStudioResult)==0){
                                echo "arrive";
                                insertStudioAlbums($dbc, $albumId, $aStudio);
                                    }
                            }
                            
                        }//end of foreach
                    }
                $albumUpdated = "Album was updated";
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
    <title>Update Album</title>
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
    <?php if(isset($albumUpdated)){echo "<span class = \"text-success align-self-center\">$albumUpdated</span>";} ?>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="albumtitle" class="form-label align-self-center mb-0 me-auto">Album Title:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($albumtitle) && !isset($albumUpdated))
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
            placeholder="0.00" required name="price" min="0" step="0.01" title="Currency" pattern="^\d+(?:\.\d{1,2})?$"
            value="<?php if(isset($albumprice) && !isset($albumUpdated))
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
            value="<?php if(isset($releasedate) && !isset($albumUpdated)){echo $releasedate;} ?>"
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