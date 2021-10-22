<?php
session_start();
$name = $_POST['username'];
$pwd = $_POST['password'];
$firstname = $_POST['firstname'];
$lname = $_POST['lname'];

$window = intval(14); //2 weeks is default value
$distance = doubleval(250);


if (isset($name) and isset($pwd) and isset($firstname)) {
    require "connect.php";

    $name = mysqli_real_escape_string($conn, $name);
    $pwd = mysqli_real_escape_string($conn, $pwd);
    $firstname = mysqli_real_escape_string($conn, $firstname);

    //if there is no last name set then set it to unspecified
    if(isset($lname))
    {
        $lname = mysqli_real_escape_string($conn, $lname);
    }
    else
    {
        $lname = "Unspecified";
    }

    //all the password validation

    //check length
    if (strlen($pwd) < 8)
    {
        echo "Password too short";
        return "The password is too short";
    }
    $lc_pass = strtolower($pwd);
    $lc_user = strtolower($name);

    //check for matching username and password
    if (strpos($lc_pass, $lc_user) !== false)
    {
        echo "Password cannot contain username";
        return "Password may not contain username";
    }

    //check through a list of common passwords to see if the password is commonly used
    $pass_file = fopen("password_list.txt", "r") or die("Unable to open file!");

    if(is_readable($pass_file))
    {
        $found = false;
        while (!($found || feof($pass_file)))
        {
            $word = fgets($pass_file, 1024);
            if ($word == $name)
            {
                $found = true;
            }
        }
        fclose($pass_file);
        if ($found)
        {
            echo "The password is commonly used, so is not allowed";
            return "The password is commonly used, so is not allowed";
        }
    }

    //check uppercase, lowercase, and numbers
    $uc; $lc; $num = 0; $other = 0;
    for ($i = 0, $j = strlen($pwd); $i < $j; $i++)
    {
        $c = substr($pwd, $i, 1);
        if(preg_match('/^[[:upper:]]$/', $c))
        {
            $uc++;
        }
        elseif(preg_match('/^[[:lower:]]$/', $c))
        {
            $lc++;
        }
        elseif(preg_match('/^[[:digit:]]$/', $c))
        {
            $num++;
        }
        else
        {
            $other++;
        }
    }
    if ($uc < 1)
    {
        $message = "Password must have at least one uppercase letter";
        echo $message;
        die();
        //header('Location: Register.html');
        //exit;
    }
    elseif ($lc < 3)
    {
        $message = 'Give at least 3 lowercase characters in your password';
        echo $message;
        die();
        //header('Location: Register.html');
        //exit;
    }
    elseif ($num < 1)
    {
        $message = 'Give at least 1 number in your password';
        echo $message;
        die();
        //header('Location: Register.html');
        //exit;
    }

    //protect from brute force by setting the cost to 10
    $options = [
        'cost' => 10,
    ];

    //encrypt password, hashing and adding salt
    $passwd = password_hash($pwd, PASSWORD_BCRYPT, $options);

    //put the user into the database
    $sql = $conn->prepare("INSERT INTO users (uname, passwd, firstname, lname, window, distance) VALUES (?, ?, ?, ?, ?, ?)");
    $sql->bind_param("ssssid", $name, $passwd, $firstname,$lname, $window, $distance);
    $sql->execute();
    
    if($result=mysqli_query($conn, $sql === false))
    {
        echo "Couldn't connect";
        die("Error2".$sql);
    }
    
    mysqli_free_result($result);
    mysqli_close($conn);
    
    //set cookies

    setcookie("uname", $name, time() + (86400 * 30), "/"); // 86400 = 1 day

    //window and distance are set to default values
    setcookie("window",$window,time() + (86400 * 30), "/");
    setcookie("distance", $distance, time() + (86400 * 30), "/"); // 86400 = 1 day

    header('Location: homepage.php');
    exit;

}
else
{
    die("Missing info");
}
?>
