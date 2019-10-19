<?php

$msg = "";
$code = 0;

include('shared.php');

if ( ! empty( $_POST ) ) {
    if ( isset( $_POST['msg'] ) ) {
        $msg = $_POST['msg'];
    }
    if ( isset( $_POST['code'] ) ) {
        $code = $_POST['code'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Virtual File Management System</title>
        <link rel="stylesheet" href="index.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
        <script src="https://kit.fontawesome.com/099a1cb3f3.js" crossorigin="anonymous"></script>
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
    <body>
    <div class="outerContainer">
    <div class="container"><div class="header-title">
			<h1>Virtual File Management System</h1>
		</div>
    <?php
    if ($code!=0) {
        echo($msg);
    }
    if (logged_in()) {
        include('home.php');
    } else {
        include('prompt.php');
    }
    ?>
		
    <div class="footer">
        <p>Virtual File Management System designed by CYBERGANG 2019</p>
    </div>
    </div>
    </div>
    </body>
</html>