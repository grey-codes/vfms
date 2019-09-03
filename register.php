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
</body>
</html>