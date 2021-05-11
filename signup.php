<?php 
    require ("functions.php");

    //check if the user has posted the form
 if($_SERVER['REQUEST_METHOD'] == "POST"){

     //get the values
     
     $firstname = $_POST['firstname'];
     $lastname = $_POST['lastname'];

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
     && !isset($invalidEmail) && !isset($invalidPass)){

         //db connection
        $dbc = dbConn();
        
        //Check if the user exists in the database     
        if(!checkUser($email)){

            //Insert users into db if the user isn't already signed up
            $insertUsers = "INSERT INTO users
            (first_name, last_name, birthday, mobile, email, password, adress)VALUES
            ('$firstname', '$lastname', '$birthday', '$mobile', '$email', '$pass', '$adress')";
               
            //Check if user was inserted into users table
            if(mysqli_query($dbc, $insertUsers)){

            //If the user is inserted we will collect the user_Id from db so we can store that in users_roles table
               $userData = checkUser($email);
               $userId = $userData['user_Id'];

               //Insert the user_Id into db table users_roles
               $insertUserRoleQuery = "INSERT INTO users_roles(role_Id, user_Id) VALUES(1, $userId)";

               //Check if the insert in the users_roles was successful
               if(mysqli_query($dbc, $insertUserRoleQuery)){
                   $userInserted = "You were signed up successfully! You can now log in.";
               }
               
            }
        }else{
            $userTaken = "You already have an account. You can log in";
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
    <title>MusicMania-SignUp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
    <link rel="stylesheet" href="./style.css">
</head>
<body>
    <div class="d-flex min-vh-100 justify-content-center align-items-center">
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?> " class="pe-2 ps-2 col-md-4 d-flex flex-column justify-content-evenly">
<?php if(isset($required)){echo "<span class = \"text-danger align-self-center\">Please fill all the required fields</span>";} ?>
<?php if(isset($userInserted)){echo "<span class = \"text-success align-self-center\">$userInserted</span>";} ?>
<?php if(isset($userTaken)){echo "<span class = \"text-warning align-self-center\">$userTaken</span>";} ?>
        <div class="d-flex justify-content-evenly mb-3">
            <label for="firstname" class="form-label align-self-center mb-0 me-auto">First Name:</label>
            <input class="form-control w-auto" type="text"
             value="<?php if(isset($firstname) && !isset($userInserted) && !isset($userTaken))
             {echo $firstname;} ?>"
            name="firstname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="lastname" class="form-label align-self-center mb-0 me-auto">Last Name:</label>
            <input type="text" class="form-control flex-grow w-auto"
             value="<?php if(isset($lastname) && !isset($userInserted) && !isset($userTaken))
             {echo $lastname;} ?>"
            name="lastname">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        
        
        <div class="d-flex justify-content-evenly mb-3">
            <label for="email" class="form-label align-self-center mb-0 me-auto">Email:</label>
            <input type="text" class="form-control w-auto"
             value="<?php if(isset($email) && !isset($userInserted) && !isset($userTaken)){echo $email;} ?>"
            name="email">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <?php if(isset($invalidEmail)){echo "<span class=\"text-danger\">$invalidEmail</span>"; }?>

        <div class="d-flex justify-content-evenly mb-3"> 
            <label for="birthday" class="form-label align-self-center mb-0 me-auto">Birthday:</label>
            <input type="date" class="form-control w-auto" 
            value="<?php if(isset($birthday) && !isset($userInserted) && !isset($userTaken)){echo $birthday;} ?>"
            name="birthday">
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="mobile" class="form-label align-self-center mb-0 me-auto">Mobile:</label>
            <input type="tel" class="form-control w-auto" 
            value="<?php if(isset($mobile) && !isset($userInserted) && !isset($userTaken)){echo $mobile;} ?>"
            name="mobile">
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="adress" class="form-label align-self-center mb-0 me-auto">Adress:</label>
            <input type="text" class="form-control w-auto" 
            value="<?php if(isset($adress) && !isset($userInserted) && !isset($userTaken)){echo $adress;} ?>"
            name="adress">
        </div>

        <div class="d-flex justify-content-evenly mb-3">
            <label for="pass" class="form-label align-self-center mb-0 me-auto">Password:</label>
            <input type="password" class="form-control w-auto" name="pass">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        
        <div class="d-flex justify-content-evenly mb-3">
            <label for="passConfig" class="form-label align-self-center mb-0 me-auto">Confirm Password:</label>
            <input type="password" class="form-control w-auto" name="passConfig">
            <div>
            <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";} ?>
            </div>
        </div>
        <?php if(isset($invalidPass)){ echo "<span class = \"text-danger align-self-center\">$invalidPass</span>";} ?>

        <div class="d-flex justify-content-evenly align-items-center mb-3">
        <input type="submit" class="btn-primary rounded" id="signUp" value="Sign Up">
        <a href="login.php" class="link-secondary">Login</a>
        </div>
    </form>
    </div>
</body>
</html>