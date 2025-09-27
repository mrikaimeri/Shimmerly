<?php

$host="localhost";
$user="shimmerlyweb";
$pass="wW[SuqB[owoWX*xQ";
$db="shimmerly";

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

?>