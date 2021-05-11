<?php
    session_start();
    require ("functions.php");

    //check if the user id logged in and admin
    checkLogin();
    checkAdmin();
    $dbc = dbConn();
    $genresResult = mysqli_query($dbc, "SELECT * FROM genres");
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        //get the values
        $studioname = $_POST['studioname'];
        $location = $_POST['location'];
        //check if email is in the right format
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
           $email = $_POST['email'];
       }else{
    
           //error message if email is not valid
           $invalidEmail  = "This email is not valid";//declare a variable that tells that email is invalid
       }
        $founddate = $_POST['founddate'];
        $mobile = $_POST['mobile'];

        //check if the fields are filled
        if(!empty($studioname) && !empty($location) &&  !empty($email) && !empty($mobile) 
        && $_FILES['studioPhoto'] && $_POST['check_list']){
            //check if the studio is in the database
            if(!checkStudio($dbc, $email)){
                $genres = $_POST['check_list'];
                
                //get the img path
                $filename = $_FILES['studioPhoto']['name'];
                $tmpName = $_FILES['studioPhoto']['tmp_name'];
                $filepath = "images/" . $filename;
                move_uploaded_file($tmpName, $filepath);
                
                //if its not we will insert into studios table
                $insertStudioQuery = "INSERT INTO studios(name, location, mobile, email, foundation_date, photoSrc)
                VALUES('$studioname', '$location','$mobile', '$email', '$founddate', '$filepath')";
                if($insertStudioResult = mysqli_query($dbc, $insertStudioQuery)){
                    //get the studioId
                    $studio = checkStudio($dbc, $email);
                    $studioId = $studio['studio_Id'];

                    //if the insertion was successful we will insert into studio_genres table
                    foreach($genres as $gen){
                        insertIntoStudioGenres($dbc, $studioId, $gen);
                    }
                $studioInserted = "Studio was inserted successfully";
                }
            }else{
                $studioTaken = "This studio already exists in the system!";
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
    <title>Add Studio</title>
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
            <a class="nav-link" href="logout.php">Log out</a> 
        </div>
    </nav>
<div class="d-flex min-vh-100 justify-content-center align-items-center">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
    enctype="multipart/form-data"
    class="pe-2 ps-2 col-md-4 d-flex flex-column justify-content-evenly">
    <?php if(isset($required)){echo "<span class = \"text-danger align-self-center\">Please fill all the required fields</span>";} ?>
    <?php if(isset($studioInserted)){echo "<span class = \"text-success align-self-center\">$studioInserted</span>";} ?>
    <?php if(isset($studioTaken)){echo "<span class = \"text-warning align-self-center\">$studioTaken</span>";} ?>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="studioname" class="form-label align-self-center mb-0 me-auto">Studio Name:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($studioname) && !isset($studioInserted) && !isset($studioTaken))
             {echo $studioname;} ?>"
            name="studioname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="location" class="form-label align-self-center mb-0 me-auto">Location:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($location) && !isset($studioInserted) && !isset($studioTaken))
             {echo $location;} ?>"
            name="location">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="mobile" class="form-label align-self-center mb-0 me-auto">Mobile:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($mobile) && !isset($studioInserted) && !isset($studioTaken))
             {echo $mobile;} ?>"
            name="mobile">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="email" class="form-label align-self-center mb-0 me-auto">Email:</label>
            <input type="text" class="form-control w-50"
             value="<?php if(isset($email) && !isset($studioInserted) && !isset($studioTaken))
             {echo $email;} ?>"
            name="email">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <?php if(isset($invalidEmail)){echo "<span class=\"text-danger\">$invalidEmail</span>"; }?>

        <div class="d-flex justify-content-evenly mb-3"> 
            <label for="founddate" class="form-label align-self-center mb-0 me-auto">Foundation Date:</label>
            <input type="date" class="form-control w-50" 
            value="<?php if(isset($founddate) && !isset($studioInserted) && !isset($studioTaken))
            {echo $founddate;} ?>"
            name="founddate">
        </div>
        
        <div class="d-flex flex-column justify-content-center">
                    <div>Music genres:</div>
                    <div class="w-100 flex-wrap"> 
                        <?php 
                            while($genre = mysqli_fetch_array($genresResult)){
                                echo "<div class=\"form-check form-check-inline\">
                                <input class=\"form-check-input\" name = \"check_list[]\" type=\"checkbox\" value=\"$genre[0]\"
                                 id=\"$genre[1]\">
                                <label class=\"form-check-label\" for=\"$genre[1]\">
                                  $genre[1]
                                </label>
                              </div>";
                            }
                        ?>
                    </div>
        </div>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="studioPhoto" class="form-label align-self-center mb-0 me-auto">Studio Logo</label>
            <input type="file" class="form-control w-50" name="studioPhoto">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
       </div>

       <div class="d-flex justify-content-center align-items-center mb-3">
        <input type="submit" class="btn-primary rounded" id="signUp" value="Add Studio">
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script> 
</body>
</html>
<?php mysqli_close($dbc);?>