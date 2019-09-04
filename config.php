<?php
session_start();
$dbhost = 'localhost:3306';
$dbuser = 'root';
$dbpass = 'pass';
$dbname = 'vfms';

$usrtb = 'userdb';

$conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

if ($conn->connect_error) {
    die("Connection failed to db.");
}

$PERM_READ = 4;
$PERM_WRITE = 2;
$PERM_EXECUTE = 1;

$usrByNameQuery = "SELECT * FROM " . $usrtb . " WHERE username = ?";
$usrByIDQuery = "SELECT * FROM " . $usrtb . " WHERE userid = ?";

$getUserByNameStatement = $conn->prepare($usrByNameQuery);
$getUserByIDStatement = $conn->prepare($usrByIDQuery);

class Perm {
    public $r = false;
    public $w = false;
    public $x = false;
}

function getPermissionContext($user, $file) {
    global $PERM_READ;
    global $PERM_WRITE;
    global $PERM_EXECUTE;
    $permAr = getOctets($file->unixperm);
    $isOwned = (($file->ownerid)==($user->userid));

    $canRead = true;
    $canWrite = true;
    $canExecute = true;

    if ($isOwned) {
        $canRead = $canRead && ( ($permAr[0] & $PERM_READ) == $PERM_READ);
        $canWrite = $canWrite && ( ($permAr[0] & $PERM_WRITE) == $PERM_WRITE);
        $canExecute = $canExecute && ( ($permAr[0] & $PERM_EXECUTE) == $PERM_EXECUTE);
    } else {
        $canRead = $canRead && ( ($permAr[2] & $PERM_READ) == $PERM_READ);
        $canWrite = $canWrite && ( ($permAr[2] & $PERM_WRITE) == $PERM_WRITE);  
        $canExecute = $canExecute && ( ($permAr[2] & $PERM_EXECUTE) == $PERM_EXECUTE);       
    }
    
    $p = new Perm();
    $p->r = $canRead;
    $p->w = $canWrite;
    $p->x = $canExecute;

    return $p;
    /*
    return array(
        "r" => $canRead,
        "w" => $canWrite,
        "x" => $canExecute
    );
    */
}

function getUserByID($uid) {
    global $getUserByIDStatement;
    $getUserByIDStatement->bind_param("i", $uid);
    $getUserByIDStatement->execute();
    $result = $getUserByIDStatement->get_result();
    $user = $result->fetch_object();
    return $user;
}

function getUserByName($un) {
    global $getUserByNameStatement;
    global $conn;
    $safeUsername = mysqli_real_escape_string($conn,$un);
    $getUserByNameStatement->bind_param("s", $safeUsername);
    $getUserByNameStatement->execute();
    $result = $getUserByNameStatement->get_result();
    $user = $result->fetch_object();
    return $user;
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