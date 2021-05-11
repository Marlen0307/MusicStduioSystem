<?php
    require("functions.php");
    session_start();

    //check if the user is logged in and admin
    checkLogin();
    checkAdmin();

    //db connection
    $dbc = dbConn();

    if(isset($_REQUEST['id'])){

        //set the studio to be updated to the session
        $_SESSION['updateStudio'] = $_REQUEST['id'];
    }

    if(isset($_SESSION['updateStudio'])){

        //get the user roles
        $studioId =  $_SESSION['updateStudio'];
        $geStudioGenresQuery = "SELECT * FROM genres g
                                JOIN studio_genres sg ON g.genre_Id = sg.genre_Id
                                WHERE sg.studio_Id = $studioId";
        $genresStudioResult = mysqli_query($dbc, $geStudioGenresQuery);
        $genresResult = mysqli_query($dbc, "SELECT * FROM genres");

    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){
        if($_POST['check_list']){
            $genres = $_POST['check_list'];
            foreach($genres as $gen){
                $genResult = mysqli_query($dbc, "SELECT * FROM studio_genres sg WHERE sg.genre_Id = $gen AND sg.studio_Id = $studioId");
                if(mysqli_num_rows($genResult)===0){

                    //we will add genre
                    $insertResult = mysqli_query($dbc, "INSERT INTO studio_genres(studio_Id, genre_Id)
                                         VALUES ($studioId,$gen)");
                }
            }
            redirect('http://localhost/Music%20Studio%20System/studioGenres.php/', 303);
            $successMessage = "Genre/s was addedd";
        }else{
            $errorMessage = "Please select at least one genre!";
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studio Genres</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body>
<div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
<?php if(isset($errorMessage)){echo"<span class = \"text-danger\">$errorMessage</span>";} ?>
<?php if(isset($successMessage)){echo"<span class = \"text-success\">$successMessage</span>";} ?>
 <table class="table w-25 table-secondary table-striped table-hover">
                <thead>
                    <tr>
                    <th class="text-center" scope="col" colspan="2">Genre</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($genreRow = mysqli_fetch_array($genresStudioResult)){
                        echo "<tr>
                                <td class = \"text-center pt-3\">". strtoupper($genreRow[1])."</td>
                                <td class = \"text-center\">
                                <a name = \"delete\" target=\"_self\" href=\"http://localhost/Music%20Studio%20System/deleteGenre.php?genreId=$genreRow[0]&id=".$studioId ."\" class=\"btn btn-danger\" role=\"button\">Delete</a>
                                </td>
                            </tr>";
                        }
                ?>
                </tbody>
</table>
<form class="align-self-center w-50 d-flex flex-column" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="POST">
<div class="d-flex flex-wrap">
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
<input  type="submit" class="btn-primary rounded mt-3 align-self-center" id="signUp" value="Add Genre">
</form>

<a class="link-info" href="http://localhost/Music%20Studio%20System/Studios.php">Go back</a>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>