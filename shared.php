<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

include('config.php');
/*
$dbhost = 'localhost:3306';
$dbuser = 'asda';
$dbname = 'asda';
$dbpass = 'asda';
*/

$usrtb = 'users';
$filetb = 'files';

$vfmsDir = "/home/vfms/vfms/";

$POSIX_FILE_PERMS = 448;

$conn = mysqli_connect($dbhost, $dbuser, $dbpass,$dbname);

if ($conn->connect_error) {
    die("Connection failed to db.");
}

$PERM_READ = 4;
$PERM_WRITE = 2;
$PERM_EXECUTE = 1;

$usrByNameQuery = "SELECT * FROM " . $usrtb . " WHERE username = ?";
$usrByIDQuery = "SELECT * FROM " . $usrtb . " WHERE userid = ?";
$fileByIDQuery = "SELECT * FROM " . $filetb . " WHERE fileid = ?";
$fileByPathQuery = "SELECT * FROM " . $filetb . " WHERE filepath = ?";
$fileAddQuery = "INSERT INTO " . $filetb . "(fileid, filepath, ownerid, unixperm) VALUES (NULL, ?, ?, ?)";
$filePermsQuery = "UPDATE " . $filetb . " SET unixperm=? WHERE fileid=?";
$fileDeleteQuery = "DELETE FROM " . $filetb . " WHERE fileid=?";

$getUserByNameStatement = $conn->prepare($usrByNameQuery);
$getUserByIDStatement = $conn->prepare($usrByIDQuery);
$getFileByIDStatement = $conn->prepare($fileByIDQuery);
$getFileByPathStatement = $conn->prepare($fileByPathQuery);
$fileAddStatement = $conn->prepare($fileAddQuery);
$filePermsStatement = $conn->prepare($filePermsQuery);
$fileDeleteStatement = $conn->prepare($fileDeleteQuery);

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
    $isOwned = false;

    if (!is_null($user)) {
        $isOwned = (($file->ownerid)==($user->userid));
    }

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

function getFileByID($fid) {
    global $getFileByIDStatement;
    $getFileByIDStatement->bind_param("i", $fid);
    $getFileByIDStatement->execute();
    $result = $getFileByIDStatement->get_result();
    $file = $result->fetch_object();
    return $file;
}

function getFileByPath($path) {
    global $getFileByPathStatement;
    global $conn;
    $safePath = mysqli_real_escape_string($conn,$path);
    $getFileByPathStatement->bind_param("s", $safePath);
    $getFileByPathStatement->execute();
    $result = $getFileByPathStatement->get_result();
    $file = $result->fetch_object();
    return $file;
}

function addFileToDB($filepath,$fileowner,$fileperms) {
    global $fileAddStatement;
    global $conn;
    $safePath = mysqli_real_escape_string($conn,$filepath);
    $fileAddStatement->bind_param("sii", $safePath, $fileowner, $fileperms);
    $fileAddStatement->execute();
    $result = $fileAddStatement->get_result();
    return $result;
}

function updateFilePerms($fileid,$fileperms) {
    global $filePermsStatement;
    $filePermsStatement->bind_param("ii", $fileperms, $fileid);
    $filePermsStatement->execute();
    $result = $filePermsStatement->get_result();
    return $result;
}

function deleteFile($fileid) {
    global $vfmsDir;
    global $fileDeleteStatement;
    $file = getFileByID($fileid);
    if (!is_null($file)) {
        $filePath = $file->filepath;
        $fullPath = $vfmsDir . $filePath;
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
        $fileDeleteStatement->bind_param("i", $fileid);
        $fileDeleteStatement->execute();
        $result = $fileDeleteStatement->get_result();
        return $result;
    }
}

function password_ver($plaintext, $hash) {
    return hash("sha512",$plaintext)==$hash;
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