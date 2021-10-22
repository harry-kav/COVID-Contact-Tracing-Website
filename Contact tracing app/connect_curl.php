<?php
error_reporting(E_ALL);
if (($handle = curl_init()) === false)
{
    echo 'Curl-Error: ' . curl_error($handle);
}
else
{
    //echo "Curl good";
    //curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($handle, CURLOPT_FAILONERROR, true);
}

?>