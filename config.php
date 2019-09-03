<?php
session_start();
$dbhost = 'localhost:3306';
$dbuser = 'root';
$dbpass = 'pass';
$dbname = 'vfms';
$tbname = 'userdb';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

if ($conn->connect_error) {
    die("Connection failed to db.");
}

function password_ver($plaintext, $hash) {
    return sha1($plaintext)==$hash;
}

function logged_in() {
    return isset($_SESSION['userid']) && !empty($_SESSION['userid']);
}

function getOctets($octal) {
    $ar = array(0,0,0);
    $ar[0] = floor($octal/64)%8;
    $ar[1] = floor($octal/8)%8;
    $ar[2] = $octal%8;
    return $ar;
}
?>