<?php
include('config.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$sessUser = getUserByID($userID);

$fid = -1;

if ( ! empty( $_POST ) ) {
    if ( isset( $_POST['fid'] ) ) {
        $fid = $_POST['fid'];
    }
}

if ($fid<0) {
    die("NO FID.");
}

$file = getFileByID($fid);
if (is_null($file)) {
    die("File is null.");
}

$absPath = $vfmsDir . $file->filepath;

if (file_exists($absPath)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($absPath).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($absPath));
    readfile($absPath);
    exit;
}

?>