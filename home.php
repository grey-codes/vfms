<script type="text/javascript" src="vfmsFrontend.js">
</script>
<script
  src="https://code.jquery.com/jquery-3.4.1.min.js"
  integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo="
  crossorigin="anonymous"></script>
<?php
require_once("libs/Mobile_Detect.php");
$detect = new Mobile_Detect();

$userID = $_SESSION['userid'];
$username = $_SESSION['username'];
$sessUser = getUserByID($userID);
?>

<div class="header-inner">
    <p>Logged in as <?php echo($username) ?><a href="logout.php">Logout</a></p>
</div>
<div class="centerContent">
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
        <?php
        if ( $detect->isMobile() ) {
            echo("</tr><tr>");
        }?>
        <td>
        </td>
        <td>
        </td>
        <td>
        </td>
    </tr>
<?php

$query = "SELECT * FROM " . $filetb;
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
    $rwxown = getPermissionContext(getUserByID($oid), $file);
    $rwxpub = getPermissionContext(NULL, $file);

    if (!is_null($owner)) {
        $ownerName = $owner->username;
    }

    $shouldDisplay = ($rwx->r || $rwx->w || $rwx->x);

    if ($shouldDisplay) {
        echo("<tr>");
        echo("<td>" . $fpath . "</td>");

        echo("<td>" . $ownerName . "</td>");
        echo("<td>");
        echo(($rwxown->r)?"r":"_");
        echo(($rwxown->w)?"w":"_");
        echo(($rwxown->x)?"x":"_");
        echo(" || ");
        echo(($rwxpub->r)?"r":"_");
        echo(($rwxpub->w)?"w":"_");
        echo(($rwxpub->x)?"x":"_");
        echo("</td>");
        if ( $detect->isMobile() ) {
            echo("</tr><tr>");
        }
        //permissions
        echo("<td><input type='button'
        value='Perms...'
        fid='".$fid."'
        vfmsAction='edit' ");
        if ($oid!=$userID) {//(!$rwx->w) {
            echo("disabled ");
        }
        echo("
        onclick=\"javascript:vfmsUse(this)\"></td>");
        //view/download
        echo("<td><input type='button'
        value='Download'
        fid='".$fid."'
        vfmsAction='view' ");
        if (!$rwx->r) {
            echo("disabled ");
        }
        echo("
        onclick=\"javascript:vfmsUse(this)\"></td>");
        //remove
        echo("<td><input type='button'
        value='Delete'
        fid='".$fid."'
        vfmsAction='delete' ");
        if (!$rwx->w) {
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
<form id="uploadForm" name="uploadForm">
    Select file to upload:
    <input type="file" name="myfile" id="myfile">
    <input type="submit" value="Upload File" name="submit">
</form>
<script type="text/javascript">
  $("#uploadForm").submit(function(evt){	
     evt.preventDefault();
     var formData = new FormData($(this)[0]);
  $.ajax({
      url: '/upload.php',
      type: 'POST',
      data: formData,
      async: false,
      cache: false,
      contentType: false,
      enctype: 'multipart/form-data',
      processData: false,
      success: function (response) {
        alert(response);
        location.reload();
      }
  });
  return false;
});
</script>