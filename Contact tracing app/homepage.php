<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" 
"http://www.w3.org/TR/html4/strict.dtd">

<html lang = "en"></html>

<head>
    <title> homepage </title>
    <meta name = "homepage" content = "homepage things" >
	<meta name = "author" content = "Harry">
	<meta charset="utf-8"/>
	
	<link rel = "stylesheet" href = "style.css">

    <script>
        //image arrays
        var red_markers = [];
        var black_markers = [];

        function place_marker(date, time, duration, id_num, colour, x, y)
            {
                
                var map = document.getElementById('map');
                var x_val = parseInt(x);
                var y_val = parseInt(y);

                if (x_val > 500 && y_val > 500)
                {
                    //infections that aren't in the 500 range are automatically excluded
                    return 'Outside range';
                }

                var img = new Image();
                var objTo = document.getElementById('marker_container');

                if(colour == "red")
                {
                    img.src = 'marker_red.png';
                }
                else
                {
                    img.src = 'marker_black.png';
                }

                img.alt = 'marker';
                img.style = "width: 20px;height: 20px; position: absolute; z-index: 11px;";
                var x_pos = (map.offsetLeft - 10) + x_val;
                var y_pos = (map.offsetTop - 20) + y_val;
                img.style.left = x_pos +'px';
                img.style.top = y_pos + 'px';
                img.style.display = 'block';

                img.onclick= function() {alert("Date: "+date+" Time: "+time+" Duration: "+duration+ " X: "+x_val+ " Y: "+y_val+ " ID: "+colour);};
                if(colour == "red")
                {
                    img.id = "marker_red_"+id_num;
                    red_markers.push(img);
                }
                else
                {
                    img.id = "marker_black_"+id_num;
                    black_markers.push(img);
                }
                objTo.appendChild(img);
            };

        function checkImages()
        {
           
            var i;
            for(i = 0; i < red_markers.length; i++)
            {
                red_markers[i].src = url('../marker_red.png');
                
            }
            for(i = 0; i < black_markers.length; i++)
            {
                black_markers[i].src = '../marker_black.png';
            }
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

    </div>

    <div class="main">
        <h2>Status</h2>
        <hr><br>
    </div>
    
    
    <div id = "page_body">

    <div id = 'map_container' style="position:relative;float:right;">
    <img src = 'exeter.jpg' id = 'map' class = "map" alt = 'exeter map' style="position:static;width:500px;height:500px; z-index: 10px;float:right;">
        <div id = 'marker_container' style="position:absolute">
        </div>

    </div>
    <div>
        <?php
            
            $user = $_COOKIE["uname"];
            $window = $_COOKIE["window"];
            $distance = $_COOKIE["distance"];

            if(isset($user) and isset($window) and isset($distance))
            {
                require "connect.php";
                //check if the user is already infected
                $sql = "SELECT date, time, duration, x, y FROM infections WHERE uname = '$user'";

                if (($result=mysqli_query($conn,$sql)) === false)
                {
                    //User not infected
                }    
                else
                {
                    if ($result->num_rows > 0) {
                        echo"<p>Hello" . $user . ". You have previously been infected. ";
                    }
                }

                if("1"=="2")
                {
                    //false condition
                }
                else
                {
                    //set up the curl connection to the web server to get infection data
                    require "connect_curl.php";

                    $url = "http://ml-lab-7b3a1aae-e63e-46ec-90c4-4e430b434198.ukwest.cloudapp.azure.com:60999/ctracker/infections.php?ts=" .$window;
                    curl_setopt($handle, CURLOPT_URL, $url);
                    curl_setopt($handle, CURLOPT_HTTPGET, true);
                    curl_setopt($handle, CURLOPT_HEADER, false);

                    if(($output = curl_exec($handle))!==false)
                    {
                        $posts = json_decode($output, true);
                        $infections = array();
                        $i = 0;
                        foreach($posts as $post)
                        {
                            
                            $post_date = $post["date"];
                            $post_time = $post["time"];
                            $post_duration = $post["duration"];
                            $post_x = $post["x"];
                            $post_y = $post["y"];
                            //get the infections and put them into an array - later they will be compared to the visits to put them on the map
                            $post_array = array("date"=>$post_date,"time"=>$post_time,"duration"=>$post_duration,"x"=>$post_x,"y"=>$post_y);
                            $infections[$i] = $post_array;
                            $i++;
                        }
                    }
                    else
                    {
                        echo 'Curl error: ' . curl_error($handle);
                        echo '<br>Unable to connect to the infections webservice. Please try again later.';
                    }
                    //get the user's visits
                    $sql = "SELECT date, time, duration, x, y FROM visits WHERE uname = '$user'";
                    if (($result=mysqli_query($conn,$sql)) === false)
                    {
                        echo "No user visits";
                    }
                    else
                    {
                        echo "<p>Hello " . $user . ". You might have had a connection to an infected person at the location(s) shown in red.</p>";
                    }
                    
                    //put the user visits into an array to compare to the infections
                    $user_visits = array();
                    $i = 0;
                    if ($result->num_rows > 0) {
                        while($row = $result->fetch_assoc()) {
                            $row_date = $row["date"];
                            $row_time = $row["time"];
                            $row_duration = $row["duration"];
                            $row_x = $row["x"];
                            $row_y = $row["y"];
                            $visits_array = array($user_visits, "date"=>$row_date,"time"=>$row_time,"duration"=>$row_duration,"x"=>$row_x,"y"=>$row_y);
                            $user_visits[$i] = $visits_array;
                        }
                        //echo $user_visits[0]["date"];
                    }

                    $num = 1;
                    //compare the infections to visits to find if they are in the same window, and to find if they are close enough to each other
                    foreach($infections as $infection)
                    {
                        foreach($user_visits as $visit)
                        {
                            $secs = abs(strtotime($visit["date"]) - strtotime($infection["date"]));
                            $days = abs($secs/86400); //convert the seconds into days

                            //if the days between the infection and visit are within the window check the distance
                            if($days <= $window)
                            {
                                //calculate the euclidean distance
                                $d = sqrt( ((int)$visit["x"] - (int)$infection["x"])**2 + ((int)$visit["y"] - (int)$infection["y"])**2);
                                
                                $date = $infection["date"];
                                $time = $infection["time"];
                                $duration = $infection["duration"];
                                $x = $infection["x"];
                                $y = $infection["y"];
                                //if the distance is acceptable, place a red marker
                                if($d <= $distance)
                                {
                                    $colour = "red";
                                    
                                }
                                else
                                {
                                    $colour = "black";
                                }
                                //place the marker via javascript
                                echo "<script type='text/javascript'> place_marker('$date','$time', '$duration', '$num', '$colour', '$x', '$y');</script>";
                                $num++;
                            }
                        }
                    }
                }
            }
                
            else{
                echo "User not logged in or window/distance not set";
            }

        ?>
        <br><br><br><br><br><br><p>Click on the markers to see details about the infection.</p><br>
        <p>© 2021 Harry Collins</p>
        </div>
    </div>


</body>