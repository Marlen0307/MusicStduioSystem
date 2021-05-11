<?php
require("functions.php");
session_start();

//check if the user role is admin
checkAdmin();
if($_SERVER['REQUEST_METHOD'] == "POST"){

    //get the values
    $userRole = $_POST['selectRole'];
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    if(isset($_POST['selectStudio']) && $_POST['selectStudio']!== 'none'){
        $studioId = $_POST['selectStudio'];
    }
    //check if email is in the right format
    if(filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)){
       $email = $_POST['email'];
   }else{

       //error message if email is not valid
       $invalidEmail  = "This email is not valid";//declare a variable that tells that email is invalid
   }
    $birthday = $_POST['birthday'];
    $mobile = $_POST['mobile'];
    $adress = $_POST['adress'];
    $pass = $_POST['pass'];
    $passConfig = $_POST['passConfig'];
    
    //Check if password is shorter than 8 chars
    if(strlen($pass) < 8){
        $invalidPass = "Password must be at least 8 characters long."; //declare a variable that tells that password is invalid   
    }

    // chechk if passwords match 
    if(strcmp($pass, $passConfig)){

       //passwords dont match
       if(isset($invalidPass)){
       
       //concatenate the invalidPass string so the user will see both errors relating to the password
       $invalidPass .=" Passwords don't match.";
       }
       else{

           //let the user know that the passwords dont match
           $invalidPass = "Passwords don't match.";
       }
  }

       //Check if the inputs required are empty and email and password are valid
   if(!empty($firstname) &&  !empty($lastname) && !empty($pass) && !empty($passConfig)
    && !isset($invalidEmail) && !isset($invalidPass) && $userRole !== 'none' && $_FILES['userPhoto']){

        //db connection
       $dbc = dbConn();

        // check if the user is already signed up
       if(!checkUser($email)){
                   //get the image path
            $filename = $_FILES['userPhoto']['name'];
            $tmpName = $_FILES['userPhoto']['tmp_name'];
            $filepath = "images/" . $filename;
           //Insert users into db if the user isn't already signed up
           $insertUsers = "INSERT INTO users
           (first_name, last_name, birthday, mobile, email, password, adress, photoSrc)VALUES
           ('$firstname', '$lastname', '$birthday', '$mobile', '$email', '$pass', '$adress', '$filepath')";
            move_uploaded_file($tmpName, $filepath);
           //Check if user was inserted into users table
           if(mysqli_query($dbc, $insertUsers)){

           //If the user is inserted we will collect the user_Id from db so we can store that in users_roles table
              $userData = checkUser($email);
              $userId = $userData['user_Id'];

              //Insert the user_Id into db table users_roles
              $insertUserRoleQuery = "INSERT INTO users_roles(role_Id, user_Id) VALUES($userRole, $userId)";
              
              //Insert the user_id and studio_id into users_studio if any studio is selected
              if(isset($studioId)){
                  $insertUserStudioQuery = "INSERT INTO users_studio(user_Id, studio_Id) VALUES ($userId, $studioId)";
                  mysqli_query($dbc, $insertUserStudioQuery);
              }
              
              
              //Check if the insert in the users_roles was successful
              if(mysqli_query($dbc, $insertUserRoleQuery)){
                  $userInserted = "User was signed up successfully!";
              }
              
           }
       }else{
            $userTaken = "This user already exits!";
       }

       //close the db connection
       mysqli_close($dbc); 
    }else{
       $required = "*";
    }

}

 ?>
 <!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta http-equiv="X-UA-Compatible" content="IE=edge">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Add User</title>
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
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" enctype="multipart/form-data"
class="pe-2 ps-2 col-md-4 d-flex flex-column justify-content-evenly">
<?php if(isset($required)){echo "<span class = \"text-danger align-self-center\">Please fill all the required fields</span>";} ?>
<?php if(isset($userInserted)){echo "<span class = \"text-success align-self-center\">$userInserted</span>";} ?>
<?php if(isset($userTaken)){echo "<span class = \"text-warning align-self-center\">$userTaken</span>";} ?>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="selectRole" class="form-label align-self-center mb-0 me-auto ">User Role</label>
            <select name="selectRole" class="form-select w-50 ps-4 pe-5">
                <option value="none">Choose the role</option>
                <?php
                    $rolesResult = mysqli_query(dbConn(),"SELECT * FROM roles");
                    while($role = mysqli_fetch_array($rolesResult)){
                        echo "<option value = \"$role[0]\">". strtoupper($role[1])."</option>";
                    }
                ?>
            </select>
            <div>
                    <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";}?>
            </div>
        </div>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="firstname" class="form-label align-self-center mb-0 me-auto">First Name:</label>
            <input class="form-control w-50" type="text"
             value="<?php if(isset($firstname) && !isset($userInserted) && !isset($userTaken))
             {echo $firstname;} ?>"
            name="firstname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="lastname" class="form-label align-self-center mb-0 me-auto">Last Name:</label>
            <input type="text" class="form-control w-50"
             value="<?php if(isset($lastname) && !isset($userInserted) && !isset($userTaken))
             {echo $lastname;} ?>"
            name="lastname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        
        
        <div class="d-flex justify-content-evenly mb-3">
            <label for="email" class="form-label align-self-center mb-0 me-auto">Email:</label>
            <input type="text" class="form-control w-50"
             value="<?php if(isset($email) && !isset($userInserted) && !isset($userTaken)){echo $email;} ?>"
            name="email">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <?php if(isset($invalidEmail)){echo "<span class=\"text-danger\">$invalidEmail</span>"; }?>

        <div class="d-flex justify-content-evenly mb-3"> 
            <label for="birthday" class="form-label align-self-center mb-0 me-auto">Birthday:</label>
            <input type="date" class="form-control w-50" 
            value="<?php if(isset($birthday) && !isset($userInserted) && !isset($userTaken)){echo $birthday;} ?>"
            name="birthday">
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="mobile" class="form-label align-self-center mb-0 me-auto">Mobile:</label>
            <input type="tel" class="form-control w-50" 
            value="<?php if(isset($mobile) && !isset($userInserted) && !isset($userTaken)){echo $mobile;} ?>"
            name="mobile">
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="adress" class="form-label align-self-center mb-0 me-auto">Adress:</label>
            <input type="text" class="form-control w-50" 
            value="<?php if(isset($adress) && !isset($userInserted) && !isset($userTaken)){echo $adress;} ?>"
            name="adress">
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="pass" class="form-label align-self-center mb-0 me-auto">Password:</label>
            <input type="password" class="form-control w-50" name="pass">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        
        <div class="d-flex justify-content-evenly mb-3">
            <label for="passConfig" class="form-label align-self-center mb-0 me-auto">Confirm Password:</label>
            <input type="password" class="form-control w-50" name="passConfig">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="photo" class="form-label align-self-center mb-0 me-auto">User Photo</label>
            <input type="file" class="form-control w-50" name="userPhoto">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="selectStudio" class="form-label align-self-center mb-0 me-auto ">User Studio</label>
            <select name="selectStudio" class="form-select w-50 ps-4 pe-5">
                <option value="none">Choose the studio</option>
                <?php
                    $studiosResult = mysqli_query(dbConn(),"SELECT * FROM studios");
                    while($studio = mysqli_fetch_array($studiosResult)){
                        echo "<option value = \"$studio[0]\">". strtoupper($studio[1])."</option>";
                    }
                ?>
            </select>
            <div>
                    <?php if(isset($required)){echo "<span class = \"align-middle text-white\">$required</span>";}?>
            </div>
        </div>
        <?php if(isset($invalidPass)){ echo "<span class = \"text-danger align-self-center\">$invalidPass</span>";} ?>

        <div class="d-flex justify-content-center align-items-center mb-3">
        <input type="submit" class="btn-primary rounded" id="signUp" value="Register">
        </div>
    </form>
    </div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
 </body>
 </html>