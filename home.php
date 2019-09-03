
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
?>

<div class="header-inner">
    <p>Logged in as <?php echo($username) ?><a href="logout.php">Logout</a></p>
</div>
<div class="filepane">
<table class="filetable">
<?php

$aclTableName = "acl";

$query = "SELECT * FROM " . $aclTableName;
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt -> get_result();
while ($row = $result->fetch_array(MYSQLI_NUM))
{
    $fid = $row[0];
    $fpath = $row[1];
    $oid = $row[2];
    $perm = $row[3];
    $permAr = getOctets($perm);

    $shouldDisplay = false;
    if ($oid == $userID && $permAr[0]>0) {
        $shouldDisplay = true;
    }
    elseif ($permAr[2]>0) {
        $shouldDisplay = true;
    }

    if ($shouldDisplay) {
        echo("<tr>");
        echo("<td>");
        var_dump($row);
        echo("</td>");
        echo("<td>");
        var_dump($permAr[0] . "," . $permAr[1] . "," . $permAr[2]);
        echo("</td>");
        echo("</tr>");
    }
}

?>
</table>
</div>