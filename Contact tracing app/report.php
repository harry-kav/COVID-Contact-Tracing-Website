<?php

//date and time of positive test
$date = $_POST['date'];
$time = $_POST['time'];
$user = $_COOKIE['uname'];


if(isset($date) and isset($time) and isset($user))
{
    require "connect_curl.php";
    $url = "http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/ctracker/report.php";
    curl_setopt($handle, CURLOPT_URL, $url);
    curl_setopt($handle, CURLOPT_HTTPHEADER, array("Content-type: application/json"));

    require "connect.php";

    //sanitize inputs
    $user = mysqli_real_escape_string($conn, $user);
    $date = mysqli_real_escape_string($conn, $date);
    $time = mysqli_real_escape_string($conn, $time);

    $sql = "SELECT date, time, duration, x, y FROM visits WHERE uname = '$user'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // post each location to the reports section of the external webservice via curl
            
            while($row = $result->fetch_assoc()) {
                $post = array("x"=>$row["x"],"y"=>$row["y"], "date"=>$row["date"],"time"=>$row["time"],"duration"=>$row["duration"]);
                curl_setopt($handle, CURLOPT_POSTFIELDS, json_encode($post));
                $server_output = curl_exec($handle);
            }
        } 
        else {
            echo "0 results";
        }
        //$conn->close();


    //sql statement to insert the infection into the database
    $sql = $conn->prepare("INSERT INTO infections(uname, date, time) VALUES (?,?,?)");
    $sql->bind_param("sss", $user, $date, $time);
    $sql->execute();

    if($result=mysqli_query($conn, $sql === false))
    {
        echo "Couldn't connect";
        die("Error2".$sql);
    }

    mysqli_free_result($result);
    mysqli_close($conn);
    header('Location: report.php');
    exit;

}
else
{
    echo "User not logged in or data not set correctly.";
}

?>