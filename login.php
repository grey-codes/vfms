<?php
include('config.php');
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
        <div class="outerContainer">
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
                <p>Virtual File Management System designed by CYBERGANG 2019</p>
            </div>
        </div>
        </div>
    </body>
</html>