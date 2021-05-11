<?php
    require("functions.php");
    session_start();
    
    //check if the user is logged in and admin
    checkLogin();
    checkAdmin();

    //db connection
    $dbc = dbConn();

    //check if the id is set
    if(isset($_REQUEST['id'])){
        $userId = $_REQUEST['id'];

        //set the user id in the session so we can use it when the form is posted
        $_SESSION['updateUser'] = $userId;

        //get the user from the db
        $getUserQuery = "SELECT u.first_name, u.last_name, u.birthday, u.mobile,
                        u.email,u.adress, us.studio_Id FROM users as u 
                        LEFT JOIN users_studio as us ON u.user_Id = us.user_Id
                        WHERE u.user_Id = $userId";
        $userReturned = mysqli_query($dbc, $getUserQuery);

        if($user = mysqli_fetch_array($userReturned)){
            //get the values 
            $firstname = $user[0];
            $lastname = $user[1];
            $birthday = $user[2];
            $mobile = $user[3];
            $email = $user[4];
            $adress = $user[5];

            //if the user has a studdio we will set it in the session
            if(!empty($user[6])){
                $selectedStudioId = $user[6];
                $_SESSION['studioId'] = $selectedStudioId; 
            }else{

                 //else set the sudioId 0 if the value from db is null
                $selectedStudioId = 0;
            }
        }
    }

    //check if the form is posted
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        //check if email is in the right format
        if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
            $email = $_POST['email'];
        }else{

            //error message if email is not valid
            $invalidEmail  = "This email is not valid";
        }

        // check if the userId is set in the SESSION
        if(isset($_SESSION["updateUser"])){
            $userId = $_SESSION["updateUser"];
        }
        
        //check if the fields are filled and the userid is set
        if(!empty($_POST['firstname']) && !empty($_POST['lastname']) && isset($email) && isset($userId) ){

            //get the values
            $firstname = $_POST['firstname'];
            $lastname = $_POST['lastname'];
            $birthday = $_POST['birthday'];
            $mobile = $_POST['mobile'];
            $adress = $_POST['adress'];
            $selectedStudioId = $_POST['selectStudio'];
            
            //updatet he user table
            $updateUserQuery = "UPDATE users as u
                                SET u.first_name = '$firstname', u.last_name = '$lastname', 
                                u.birthday = '$birthday',u.mobile = '$mobile', u.email = '$email',
                                 u.adress = '$adress'
                                WHERE U.user_Id = $userId";
            $resultUserUpdate = mysqli_query($dbc, $updateUserQuery);
            
            //check if the user is without a studio
            if(isset($_SESSION['studioId'])){
               //if a user that had a studio, but now hasent will be deleted from users_studio
                if($selectedStudioId == 0){
                    $userStudioDeleteResult = mysqli_query($dbc, "DELETE FROM users_studio WHERE user_Id = $userId");
                    unset($_SESSION['studioId']);
                }else{
                         //if the user is in a studio and now has another one we will update
                $updateUserStudioQuery = "UPDATE users_studio AS ur
                                            SET ur.studio_Id = $selectedStudioId
                                            WHERE ur.user_Id = $userId";
                $userStudioUpdateResult = mysqli_query($dbc, $updateUserStudioQuery);
                if($userStudioUpdateResult){$_SESSION['studioId'] = $selectedStudioId;}
                }
               
            }else{
    
                //else we will insert
                $insertUserStudioQuery = "INSERT INTO users_studio(user_Id, studio_Id) VALUES($userId, $selectedStudioId)";
                $userStudioInsertResult = mysqli_query($dbc, $insertUserStudioQuery);
                if($userStudioInsertResult){$_SESSION['studioId'] = $selectedStudioId;}
            }

            // if the querys were successful we will let the admin know
            if(isset($userStudioInsertResult) || isset($userStudioUpdateResult) || isset($userStudioDeleteResult)){
                $userUpdated = "User Updated Successfully";
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
    <title>Update User</title>
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data"
class="pe-2 ps-2 col-md-4 d-flex flex-column justify-content-evenly">
<?php if(isset($userUpdated)){echo "<span class = \"text-success align-self-center\">$userUpdated</span>";} ?>
<?php if(isset($userTaken)){echo "<span class = \"text-warning align-self-center\">$userTaken</span>";} ?>
<?php if(isset($required)){echo "<span class = \"text-danger align-self-center\">Please fill all the required fields</span>";} ?>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="firstname" class="form-label align-self-center mb-0 me-auto">First Name:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($firstname) && !isset($userTaken))
             {echo $firstname;} ?>"
            name="firstname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="lastname" class="form-label align-self-center mb-0 me-auto">Last Name:</label>
            <input type="text" class="form-control w-50"
             value="<?php if(isset($lastname) && !isset($userTaken))
             {echo $lastname;} ?>"
            name="lastname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        
        
        <div class="d-flex justify-content-evenly mb-3">
            <label for="email" class="form-label align-self-center mb-0 me-auto">Email:</label>
            <input type="text" class="form-control w-50"
             value="<?php if(isset($email) && !isset($userTaken)){echo $email;} ?>"
            name="email">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <?php if(isset($invalidEmail)){echo "<span class=\"text-danger\">$invalidEmail</span>"; }?>

        <div class="d-flex justify-content-evenly mb-3"> 
            <label for="birthday" class="form-label align-self-center mb-0 me-auto">Birthday:</label>
            <input type="date" class="form-control w-50" 
            value="<?php if(isset($birthday) && !isset($userTaken)){echo $birthday;} ?>"
            name="birthday">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-white\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="mobile" class="form-label align-self-center mb-0 me-auto">Mobile:</label>
            <input type="tel" class="form-control w-50" 
            value="<?php if(isset($mobile) && !isset($userTaken)){echo $mobile;} ?>"
            name="mobile">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-white\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="adress" class="form-label align-self-center mb-0 me-auto">Adress:</label>
            <input type="text" class="form-control w-50" 
            value="<?php if(isset($adress) && !isset($userTaken)){echo $adress;} ?>"
            name="adress">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-white\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="selectStudio" class="form-label align-self-center mb-0 me-auto ">User Studio</label>
            <select name="selectStudio" class="form-select w-50 ps-4 pe-5">
                <option value="0">None</option>
                <?php
                    $studiosResult = mysqli_query($dbc,"SELECT * FROM studios");
                    while($studio = mysqli_fetch_array($studiosResult)){
                        echo "<option value = \"$studio[0]\"";if(isset($selectedStudioId) && $selectedStudioId === $studio[0]){echo 'selected';}echo">".strtoupper($studio[1]) ."</option>";
                    }
                ?>
            </select>
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-white\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-center align-items-center mb-3">
        <input type="submit" class="btn-primary rounded" id="signUp" value="Update">
        </div>
    </form>

    <?php mysqli_close($dbc);
    ?>
</body>
</html>