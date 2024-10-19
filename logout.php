<?php 
include 'logger.php'; 

session_start();
session_destroy();

writelog("logout successful");
header("Location: index.php")

?>