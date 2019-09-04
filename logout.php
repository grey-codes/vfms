<?php
include('config.php');
session_destroy(); // Is Used To Destroy All Sessions
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<title>Virtual File Management System</title>
        <link rel="stylesheet" href="index.css">
        <link href="https://fonts.googleapis.com/css?family=Roboto&display=swap" rel="stylesheet">
		<meta charset="UTF-8">
		<meta name="viewport" content="width=device-width, initial-scale=1">
      <script type="text/javascript">
      setTimeout(function () {
         window.location.href = "/";
      }, 2000);
      </script>
	</head>
    <body>
        <div class="outerContainer">
        <div class="container">
            <div class="header-title">
                <h1>Virtual File Management System</h1>
            </div>
         <h1>Logged out.</h1>
         <div class="footer">
            <p>Virtual File Management System designed by CYBERGANG 2019</p>
         </div>
        </div>
   </body>
</html>