<?php
include('config.php');

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
    die("invalid fid");
}

$rwx = getPermissionContext($sessUser, $file);

if (!$rwx->w) {
    die("No write permissions!");
}

deleteFile($fid);
echo("Successfully deleted.");
?>