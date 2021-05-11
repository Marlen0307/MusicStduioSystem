<?php
    require("functions.php");
    session_start();
    //check if the user is logged in and admin
    checkLogin();
    checkAdmin();

    //db connection
    $dbc = dbConn();

    if(isset($_REQUEST['id'])){

        //set the user to be updated to the session
        $_SESSION['updateUser'] = $_REQUEST['id'];
    }

    if(isset($_SESSION['updateUser'])){

        //get the user roles
        $userId =  $_SESSION['updateUser'];
        $getUserRolesQuery = "SELECT * FROM roles as r
                                 JOIN users_roles as ur ON r.role_Id = ur.role_Id
                                  WHERE ur.user_Id = $userId";
        $rolesResult = mysqli_query($dbc, $getUserRolesQuery);
    }
    if($_SERVER['REQUEST_METHOD'] == "POST"){

        // check if a role was selected
        if($_POST['selectRole']){

            //get the role and the user id
            $role = $_POST['selectRole'];
            $userId = $_SESSION['updateUser'];

            //if the user alreday has one certain role the insertion can not continue
            if(checkUserandRole($userId, $role)){
                $userRoleExists = "This user already has this role";
            }else{

            // else we will insert into users roles
                $insertUserRoleQuery = "INSERT INTO users_roles(role_Id, user_Id) VALUES($role, $userId)";

                //Check if the insert in the users_roles was successful
                if(mysqli_query($dbc, $insertUserRoleQuery)){

                    // if the insertion was successful we will let the admin know and refresh the page
               $userRoleInserted = "Role was added successfully!";
               redirect("http://localhost/Music%20Studio%20System/userRoles.php", 303);
                }
        
            }
        }else{

            //if no role was selected we will let the admin know
            $errorMessage = "Please select a role";
        }
    }
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Roles</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-eOJMYsd53ii+scO/bJGFsiCZc+5NDVN2yr8+0RDqr0Ql0h+rP48ckxlpbzKgwra6" crossorigin="anonymous">
</head>
<body>
<div class="d-flex flex-column min-vh-100 justify-content-center align-items-center">
<?php if(isset($errorMessage)){echo"<span class = \"text-danger\">$errorMessage</span>";} ?>
<?php if(isset($userRoleInserted)){echo"<span class = \"text-success\">$userRoleInserted</span>";} ?>
<?php if(isset($userRoleExists)){echo"<span class = \"text-warning\">$userRoleExists</span>";} ?>
<table class="table table-secondary w-25 table-striped table-hover">
                <thead>
                    <tr>
                    <th class="text-center" scope="col" colspan="2">Role</th>
                    </tr>
                </thead>
                <tbody>
                <?php while($roleRow = mysqli_fetch_array($rolesResult)){
                        echo "<tr>
                                <td class = \"text-center pt-3\">". strtoupper($roleRow[1])."</td>
                                <td class = \"text-center\">
                                <a name = \"delete\" target=\"_self\" href=\"http://localhost/Music%20Studio%20System/deleteRole.php?roleId=$roleRow[0]&id=".$userId ."\" class=\"btn btn-danger\" role=\"button\">Delete</a>
                                </td>
                            </tr>";
                        }
                ?>
                </tbody>
</table>
            
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <div class="d-flex justify-content-between">
                <label for="selectRole" class="form-label align-self-center mb-0 me-auto ">User Role</label>
                <select name="selectRole" class="form-select w-50 ps-4 pe-5">
                    <option value="0">Choose the role</option>
                    <?php
                        $rolesResult = mysqli_query($dbc,"SELECT * FROM roles");
                        while($role = mysqli_fetch_array($rolesResult)){
                            echo "<option value = \"$role[0]\">". strtoupper($role[1])."</option>";
                        }
                    ?>
                </select>
                <div>
                        <?php if(isset($required)){echo "<span class = \"align-middle text-danger\">$required</span>";}?>
                </div>
                
                    <input type="submit" class="btn-primary rounded" id="signUp" value="Add Role">
        </div>
    </form>
    <div><a href="http://localhost/Music%20Studio%20System/Users.php" class="nav-link">Go Back</a></div>
</div>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js" integrity="sha384-SR1sx49pcuLnqZUnnPwx6FCym0wLsk5JZuNx2bPPENzswTNFaQU1RDvt3wT4gWFG" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta3/dist/js/bootstrap.min.js" integrity="sha384-j0CNLUeiqtyaRmlzUHCPZ+Gy5fQu0dQ6eZ/xAww941Ai1SxSY+0EQqNXNE6DZiVc" crossorigin="anonymous"></script>
</body>
</html>