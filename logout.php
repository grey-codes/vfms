<?php
include('config.php');
session_destroy(); // Is Used To Destroy All Sessions
?>
<html>
<head>
<title>Logged out.</title>
<script type="text/javascript">
setTimeout(function () {
   window.location.href = "/";
}, 2000);
</script>
</head>
<body>
<h1>Logged out.</h1>
</body>
</html>