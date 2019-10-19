<?php
include('shared.php');

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$sessUser = getUserByID($userID);

$fid = -1;
$octal = -1;

if ( ! empty( $_POST ) ) {
    if ( isset( $_POST['fid'] ) ) {
        $fid = $_POST['fid'];
    }
    if ( isset( $_POST['octal'] ) ) {
        $octal = $_POST['octal'];
    }
}

if ($fid<0) {
    die("NO FID.");
}

if ($octal<0) {
    die("NO OCTAL.");
}

$file = getFileByID($fid);
if (is_null($file)) {
    die("invalid fid");
}

if ($file->ownerid != $userID) {
    die("You don't own that file.");
}

$rwx = getPermissionContext($sessUser, $file);

if (!$rwx->w) {
    die("No write permissions!");
}

updateFilePerms($file->fileid,$octal);
echo("Successfully updated.");
?>