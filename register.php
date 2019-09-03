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
        <div class="container">
            <div class="header-title">
                <h1>Virtual File Management System</h1>
            </div>
            <?php
            if ( ! empty( $_POST ) ) {
                if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
                    $safeUsername = mysqli_real_escape_string($conn,$_POST['username']);
                    $query = "SELECT * FROM " . $tbname . " WHERE username = '" . $safeUsername . "'";
                    $stmt = $conn->prepare($query);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $user = $result->fetch_object();
                    
                    if ($user === NULL) {
                        $hash = sha1($_POST['password']);
                        $insQuery = "INSERT INTO userdb (userid, username, passhash) VALUES (NULL, '" . $safeUsername . "', '" . $hash . "')";
                        $insSt = $conn->prepare($insQuery);
                        $insSt->execute();
                        echo("<h1>User created. Please log in.</h1>");
                    } else {
                        echo("<h1>User already exists!</h1>");
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