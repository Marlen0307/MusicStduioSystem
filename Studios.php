<?php 
    session_start();
    require ("functions.php");
    checkLogin();
    checkAdmin();
    $dbc = dbConn();
    $studiosResult = mysqli_query($dbc, "SELECT * FROM studios");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Studios</title>
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
                    echo "<a href =\"http://localhost/Music%20Studio%20System/addStudio.php\" class = \"nav-link\">Add Studio</a>";
                    echo "<a href=\"http://localhost/Music%20Studio%20System/Studios.php\" class=\"nav-link\">Studios</a>";
                    }if(isset($_SESSION['role']) && $_SESSION['role'] == 'artist'){
                        echo "<a href =\"http://localhost/Music%20Studio%20System/artistSongs.php\" class = \"nav-link\">Your Songs</a>";
                        echo "<a href =\"http://localhost/Music%20Studio%20System/artistAlbum.php\" class = \"nav-link\">Your Albums</a>";;
                    } ?>
            </div>
            <a class="nav-link" href="logout.php">Log out</a> 
        </div>
    </nav>
    <div class="container">
            <table class="table table-dark table-striped table-hover">
                <thead>
                    <tr>
                    <th scope="col">Id</th>
                    <th scope="col">Studio</th>
                    <th scope="col">Location</th>
                    <th scope="col">Mobile</th>
                    <th scope="col">Email</th>
                    <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($studioRow = mysqli_fetch_array($studiosResult)){
                        echo "<tr>
                                <th scope=\"row\">$studioRow[0]</th>
                                <td>$studioRow[1]</td>
                                <td>$studioRow[2]</td>
                                <td>$studioRow[3]</td>
                                <td>$studioRow[4]</td>
                                <td>
                                <a name = \"delete\" href=\"deleteStudio.php/?id=$studioRow[0]\" class=\"btn btn-danger\" role=\"button\">Delete</a>
                                <a name = \"update\" href=\"updateStudio.php/?id=$studioRow[0]\" class=\"btn btn-success\" role=\"button\">Update</a>
                                <a name = \"roles\" href=\"studioGenres.php/?id=$studioRow[0]\" class=\"btn btn-primary\" role=\"button\">Genres</a>
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