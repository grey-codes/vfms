<?php
include('config.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Virtual File Management System</title>
        <link rel="stylesheet" href="index.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
	</head>
    <body>
        <div class="container">
            <div class="header-title">
                <h1>Virtual File Management System</h1>
            </div>
            <?php
            if ( ! empty( $_POST ) ) {
                if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
                    $user = getUserByName($_POST['username']);
                        
                    if ( !is_null($user) && password_ver( $_POST['password'], $user->passhash ) ) {
                        $_SESSION['userid'] = $user->userid;
                        $_SESSION['username'] = $user->username;
                        echo("<h1>Login verified.</h1>");
                    } else {
                        echo("<h1>Invalid username or password.</h1>");
                    }
                }
            }
            ?>

            <script type="text/javascript">
            setTimeout(function () {
            window.location.href = "/";
            }, 2000);
            </script>
                
            <div class="footer">
                <p>Virtual File Management System designed by Grey Ruessler, Quinn Johnson,
                Logan Geppert, and Sawyer Loos 2019</p>
            </div>
        </div>
    </body>
</html>