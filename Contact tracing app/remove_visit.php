<?php
require "connect.php";

//decode the json and create variables from the data
$in = file_get_contents('php://input');
$decoded = json_decode($in, true);

$name = $_COOKIE["uname"];
$date = $decoded["date"];

$time = $decoded["time"];

$duration = $decoded["duration"];

$x = $decoded["x"];

$y = $decoded["y"];


if(isset($name) and isset($date) and isset($time) and isset($duration) and isset($x) and isset($y))
{

    //sql delete statement
    $sql = $conn->prepare("DELETE FROM visits WHERE uname =? AND date=? AND time=? AND duration=? AND x=? AND y=?");

    $sql->bind_param('sssiii',$name, $date, $time, $duration, $x, $y);

    $sql->execute();

    echo("Visit removed successfully");
    //echo "executed";
    mysqli_close($conn);
    //header('Location: overview.php');
}
else
{
    echo "Missing data";
    mysqli_close($conn);
    return "Insufficient data given";
}

//$conn->close();
//header('Location: overview.php');
//exit;

?>