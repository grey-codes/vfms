<?php
include('config.php');
?>
<html>
<head>
<title>Logging in...</title>
</head>
<body>
<?php
if ( ! empty( $_POST ) ) {
    if ( isset( $_POST['username'] ) && isset( $_POST['password'] ) ) {
        $safeUsername = mysqli_real_escape_string($conn,$_POST['username']);
        $query = "SELECT * FROM " . $tbname . " WHERE username = '" . $safeUsername . "'";
        $stmt = $conn->prepare($query);
        $stmt->execute();
        $result = $stmt->get_result();
    	$user = $result->fetch_object();
            
    	if ( password_ver( $_POST['password'], $user->passhash ) ) {
            $_SESSION['userid'] = $user->userid;
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
</body>
</html>