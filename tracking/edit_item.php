<?php
session_start();
require_once'connect.php';

//check if user is logged in
if(isset($_SESSION['user_id'])){
    header("Location:login.php");
    exit();
}
$userid=$_SESSION['user_id'];

