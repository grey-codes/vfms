<script type="text/javascript" src="vfmsFrontend.js">
</script>
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$sessUser = getUserByID($userID);
?>

<div class="header-inner">
    <p>Logged in as <?php echo($username) ?><a href="logout.php">Logout</a></p>
</div>
<div class="filepane">
<table class="filetable">
    <tr>
        <td>
            File Path
        </td>
        <td>
            File Owner
        </td>
        <td>
            File Permissions
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
<?php

$aclTableName = "acl";

$query = "SELECT * FROM " . $aclTableName;
$stmt = $conn->prepare($query);
$stmt->execute();
$result = $stmt -> get_result();
while ($file = $result->fetch_object())
{
    $fid = $file -> fileid;
    $fpath = $file -> filepath;
    $oid = $file -> ownerid;
    $perm = $file -> unixperm;

    $owner = getUserByID($oid);
    $ownerName = "null";

    $rwx = getPermissionContext($sessUser, $file);

    if (!is_null($owner)) {
        $ownerName = $owner->username;
    }

    $shouldDisplay = ($rwx->r || $rwx->w || $rwx->x);

    if ($shouldDisplay) {
        echo("<tr>");
        echo("<td>" . $fpath . "</td>");

        echo("<td>" . $ownerName . "</td>");
        echo("<td>");
        echo(($rwx->r)?"r":"_");
        echo(($rwx->w)?"w":"_");
        echo(($rwx->x)?"x":"_");
        echo("</td>");
        echo("<td><input type='button'
        value='Edit'
        fid='".$fid."'
        vfmsAction='edit' ");
        if (!$rwx->w) {
            echo("disabled ");
        }
        echo("
        onclick=\"javascript:vfmsUse(this)\"></td>");
        echo("<td><input type='button'
        value='View'
        fid='".$fid."'
        vfmsAction='view' ");
        if (!$rwx->r) {
            echo("disabled ");
        }
        echo("
        onclick=\"javascript:vfmsUse(this)\"></td>");
        echo("</tr>\n");
    }
}

?>
</table>
</div>