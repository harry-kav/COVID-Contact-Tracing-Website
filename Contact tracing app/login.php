<?php
// Initialize the session
session_set_cookie_params(0);
session_start();

$name = $_POST['username'];
$pwd = $_POST['password'];


if (isset($name) and isset($pwd)) {
    require "connect.php";

    //prevent sql injection
    $name = mysqli_real_escape_string($conn, $name);
    $pwd = mysqli_real_escape_string($conn, $pwd);
    
    //check username and password- login user if successful
    $sql = "SELECT passwd FROM users WHERE uname = '$name'";

    if (($result=mysqli_query($conn,$sql)) === false)
        die("Error executing ".$sql);
    if (mysqli_num_rows($result)!==1)
        die("username does not exist");

    $user = mysqli_fetch_row($result);


    $sql = "SELECT window FROM users WHERE uname = '$name'";
    
    //set the window and distance cookies

    if (($result=mysqli_query($conn,$sql)) === false)
        die("Error executing ".$sql);
    if (mysqli_num_rows($result)!==1)
        die("username does not exist");

    $window = mysqli_fetch_row($result);

    $sql = "SELECT distance FROM users WHERE uname = '$name'";
    

    if (($result=mysqli_query($conn,$sql)) === false)
        die("Error executing ".$sql);
    if (mysqli_num_rows($result)!==1)
        die("username does not exist");

    $distance = mysqli_fetch_row($result);

    //if the password is correct, login and set cookies
    if(password_verify($pwd,$user[0]))
    {
        $_SESSION["user"] = $user;
        setcookie("uname", $name, time() + (86400 * 30), "/"); // 86400 = 1 day
        setcookie("window",$window[0],time() + (86400 * 30), "/");
        setcookie("distance", $distance[0], time() + (86400 * 30), "/"); // 86400 = 1 day

        mysqli_free_result($result);
        mysqli_close($conn);
        header('Location: homepage.php');
        exit;
    }
    else
    {
        die("Incorrect password. Please try again");
    }

}
?>