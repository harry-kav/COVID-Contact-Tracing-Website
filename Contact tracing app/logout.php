<?php

//reset all cookies and end the session, before going to the login screen
setcookie("uname","", time()-3600);
setcookie("window","", time()-3600);
setcookie("distance","", time()-3600);
unset($_COOKIE['uname']);
unset($_COOKIE['window']);
unset($_COOKIE['distance']);

session_unset();
session_destroy();

header('Location: login.html');
exit;

?>