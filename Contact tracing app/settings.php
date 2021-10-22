<?php

$window = $_POST['window'];
$distance = $_POST['distance'];
$user = $_COOKIE["uname"];


if(isset($window) and isset($distance) and isset($user))
{
    require "connect.php";

    $window = mysqli_real_escape_string($conn, $window);
    $distance = mysqli_real_escape_string($conn, $distance);
    $user = mysqli_real_escape_string($conn, $user);

    //update the user's settings via sql update
    $sql = $conn->prepare("UPDATE users SET window=?, distance=? WHERE uname=?");
    $sql->bind_param("ids", $window, $distance, $user);
    $sql->execute();

    if (($result=mysqli_query($conn,$sql)) === false)
        die("Error executing ".$sql);


    setcookie("window",$window,time() + (86400 * 30), "/");
    setcookie("distance", $distance, time() + (86400 * 30), "/"); // 86400 = 1 day

    mysqli_free_result($result);
    mysqli_close($conn);
    header('Location: settings.html');
    exit;

}
else{
    die("User not logged in or window/distance not set");
}


?>