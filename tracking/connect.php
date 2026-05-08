<?php
$server="localhost";
$username="root";
$pass="root";
$database="tracking_db";
$con= mysqli_connect($server,$username,$pass,$database);
if ($con)
{
   // echo"successfully connected";
}
else
{
die("erro conecting to the server".mysqli_error($con));
}
?>