<?php
    session_start();
    require("functions.php");
    checkAdmin();
    $dbc = dbConn();
    if(isset($_REQUEST['id']) && isset($_REQUEST['roleId'])){
        $roleId = $_REQUEST['roleId'];
        $userId = $_REQUEST['id'];
        if(checkUserandRole($userId, $roleId)){
            $deleteQuery = "DELETE FROM `users_roles` WHERE user_Id = $userId AND role_Id = $roleId";
            $deleteResult = mysqli_query($dbc, $deleteQuery);
            if($deleteResult){
                mysqli_close($dbc);
                redirect("http://localhost/Music%20Studio%20System/userRoles.php/?id=".$userId, 303);
            }
        }
    }
?>