<?php
require ("functions.php");
session_start();

//unset the session so the user cannot enter the index.php without loging in again
session_unset();
if($_SERVER['REQUEST_METHOD'] == "POST"){

    //Get the values
    $email = $_POST['email'];
    $password = $_POST['pass'];

    //check if email and password have values
    if(!empty($email) && !empty($password)){

        //check if the user exists
        if($userData = checkUser($email)){
          
        //if the user exists we will check if the password is right
            if($userData['password'] === $password ){
                //set the user_Id in the SESSION
                $userId = $userData['user_Id'];
                $_SESSION['user_Id'] = $userId;

                //get the user roles
                $selectRole = "SELECT role FROM roles as r
                INNER JOIN users_roles as ur ON ur.role_Id = r.role_Id
                INNER JOIN users as u ON u.user_Id = ur.user_Id
                WHERE u.user_Id = $userId";
                $resultRole =  mysqli_query(dbConn(), $selectRole);
                while($role = mysqli_fetch_array($resultRole)){

                    //check if the user is admin
                    if ($role['role'] == 'admin'){

                        //if the user is admin set the user role in the session
                        $_SESSION['role'] = 'admin';
                    }

                    if($role['role'] == 'artist'){
                        //if the user is artist set the use role in the session
                        $_SESSION['role'] = 'artist';
                    }
                    
                }

                //redirect the user to the index page if the login was successful
                redirect('http://localhost/Music%20Studio%20System/index.php', 303);
            }else{

                //if the password is incorrect we will let the user know
                $wrongValues = "Wrong password!";
            }
        }else{

            //if the user with this email doesn't exists we will let the user know
            $wrongValues = "Wrong email!"; 
        } 
    }else{
        $wrongValues = "Please write your email and password!";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicMania-Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">

</head>
<body>
   <div class="col-12 min-vh-100 d-flex justify-content-center align-items-center">
   <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>"
    class="d-flex flex-column p-3 col-sm-4 col-xl-3 justify-content-center border rounded border-secondary">
   <?php if(isset($wrongValues)){echo "<span class =\"text-danger align-self-center\">$wrongValues</span>";} ?>
    <div class="d-flex justify-content-between">
        <label for="email" class="form-label" >Email:</label>
        <input type="text" name="email" class="form-control w-auto">
    </div>
    <div class="d-flex justify-content-between mt-3">
        <label for="pass" class="form-label">Password: </label>
        <input type="password" name="pass" class="form-control w-auto">
    </div>
    <div class="d-flex flex-column mt-3 align-items-center">
    <input type="submit" name="logIn" class="btn-primary col-3 rounded" value="Log In">
    <span class="text-secondary">Don't have an account?<a href="signup.php" class="link-primary fw-bold">Sign Up</a></span>
    </div>
    </form>
   </div>
</body>
</html>