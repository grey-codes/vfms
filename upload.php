<?php
include('config.php');

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$sessUser = getUserByID($userID);

$filePerms = 448;
$uploadDirectory = $vfmsDir;

$errors = []; // Store all foreseen and unforeseen errors here.

$fileExtensions = ['jpeg','jpg','png']; // Get all the file extensions.

if (!array_key_exists('myfile',$_FILES)) {
    die("my file doesn't exist");
}

$fileName = $_FILES['myfile']['name'];
$fileSize = $_FILES['myfile']['size'];
$fileTmpName  = $_FILES['myfile']['tmp_name'];
$fileType = $_FILES['myfile']['type'];
//$fileExtension = strtolower(end(explode('.',$fileName)));

$uploadPath = $uploadDirectory . basename($fileName); 
$uploadPathFull = $uploadDirectory . $fileName; 

$existingFile = getFileByPath($fileName);
if (!is_null($existingFile)) {
    $rwx = getPermissionContext($sessUser, $existingFile);
    if (!($rwx->w)) {
        die("File already exists; no write access!");
    }
}

if (isset($fileName)) {

    /*
    if (! in_array($fileExtension,$fileExtensions)) {
        $errors[] = "This process does not support this file type. Upload a JPEG or PNG file only.";
    }*/

    if (empty($errors)) {
        $didUpload = move_uploaded_file($fileTmpName, $uploadPath);

        if ($didUpload) {
            chmod($uploadPathFull, $POSIX_FILE_PERMS);
            if (is_null($existingFile)) {
                addFileToDB($fileName,$userID,$filePerms);
            }
            echo "The file " . basename($fileName) . " has been uploaded.";
        } else {
            echo "An error occurred. Try again or contact your system administrator.";
        }
    } else {
        foreach ($errors as $error) {
            echo $error . "These are the errors" . "\n";
        }
    }
}


?>
