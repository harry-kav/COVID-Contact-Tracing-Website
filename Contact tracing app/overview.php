<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/strict.dtd">

<html lang = "en"></html>

<head>
    <title> login </title>
    <meta name = "overview" content = "table of visits" >
	<meta name = "author" content = "Harry">
	<meta charset="utf-8"/>
	
	<link rel = "stylesheet" href = "style.css">


    <script>
    function dbDelete(row_num) {
        var table = document.getElementById("visit_table");
 
        //get the table data
        var date = table.rows[row_num].cells[0].innerHTML;
        var time = table.rows[row_num].cells[1].innerHTML;
        var duration = table.rows[row_num].cells[2].innerHTML;
        var x = table.rows[row_num].cells[3].innerHTML;
        var y = table.rows[row_num].cells[4].innerHTML;

        var params = {"date":date,"time":time,"duration":duration,"x":x,"y":y};
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
            alert(this.responseText);
            }
        };
        //delete the row from the database using ajax
        xmlhttp.open("POST", "remove_visit.php", true);
        xmlhttp.setRequestHeader("Content-type", "application/json; charset=UTF-8;");
        xmlhttp.send(JSON.stringify(params));
        //delete the row from the table
        document.getElementById("visit_table").deleteRow(row_num);
        alert("Visit deleted")

    }
    </script>

</head>

<body>

    <h1> COVID-19 Contact Tracing</h1>

    <div class = "sidebar">

        <a href="homepage.php"><button>Home</button></a><br>
        <a href="overview.php"><button>Overview</button></a><br>
        <a href="add_visit.html"><button>Add Visit</button></a><br>
        <a href="report.html"><button>Report</button></a><br>
        <a href="settings.html"><button>Settings</button></a><br><br><br>
        <a href="logout.php"><button>Logout</button></a>

    </div><br>

    <div class="main">
        <div class = "main">
            <h2>Overview</h2>
            <hr>
            <p>© 2021 Harry Collins</p><br>
        </div>
    <table id= "visit_table" style = "width: 75%;";>
    <tr>
    <th>Date</th>
    <th>Time</th>
    <th>Duration</th>
    <th>X</th>
    <th>Y</th>
    <th>Delete</th>
    </tr>
        <?php
            $user = $_COOKIE["uname"];
            require "connect.php";

            if(!isset($user))
            {
                return "No user logged in";
            }

            $sql = "SELECT date, time, duration, x, y FROM visits WHERE uname = '$user'";

            if (($result=mysqli_query($conn,$sql)) === false)
                die("Error executing ".$sql);

            if ($result->num_rows > 0) {
            // output data of each row
                $i = 1;
                while($row = $result->fetch_assoc()) {

                    echo "<tr><td>" . $row["date"]. "</td><td>" . $row["time"]. "</td><td>" . $row["duration"]. "</td><td>" . $row["x"]. "</td><td>" . $row["y"]. "</td><td><img src = 'cross.png' style='width:10px;height:10px;' onclick = 'dbDelete($i)'></img></td></tr>" ;
                    $i++;
                }
            } 
            else {
                echo "0 results";
            }
            $conn->close();
        ?>
    </div>


</body>