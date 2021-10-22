<?php
require "connect.php";

$date = strval($_POST['date']);
$time = strval($_POST['time']);
$duration = intval($_POST['duration']);
$x = intval($_POST['x_pos']);
$y = intval($_POST['y_pos']);
$name = $_COOKIE["uname"];

if (isset($date) and isset($time) and isset($duration) and isset($x) and isset($y)and isset($name))
{

    $date = mysqli_real_escape_string($conn, $date);
    $time = mysqli_real_escape_string($conn, $time);
    $duration = intval(mysqli_real_escape_string($conn, $duration));
    $x = intval(mysqli_real_escape_string($conn, $x));
    $y = intval(mysqli_real_escape_string($conn, $y));
    $name = mysqli_real_escape_string($conn, $name);


    //add visit to database
    $sql = $conn->prepare("INSERT INTO visits(uname, date, time, duration, x, y) VALUES (?,?,?,?,?,?)");
    $sql->bind_param("sssiii", $name, $date, $time, $duration, $x, $y);
    $sql->execute();

    if($result=mysqli_query($conn, $sql === false))
    {
        echo "failed to add";
        die("Error2".$sql);
    }

    echo "finished";
    mysqli_free_result($result);
    mysqli_close($conn);
    header('Location: add_visit.html');
    exit;

    
}
else
{
    die("User not logged in or data missing or invalid data");
}

?>