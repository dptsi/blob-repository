<?php
error_reporting(1);
require_once 'vendor/autoload.php';

use MyITS\BlobRepository\BlobRepository;
$blobUpload = new BlobRepository('SSO Provider', 'SSO Client ID', 'SSO Secret');

if (isset($_POST["submit"])) {
    $d = $blobUpload->storeFile($_FILES['fileToUpload']);
    var_dump($d->usual());
}

if (isset($_POST["update"]) && isset($_POST["file_id"])) {
    $d = $blobUpload->updateFile($_FILES['fileToUpload'], $_POST["file_id"]);
    var_dump($d->usual());
}

?>
<!DOCTYPE html>
<html>
<body>
<h3>Upload file</h3>
<form action="" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

<h3>Update File</h3>
<form action="" method="post" enctype="multipart/form-data">
    File ID: <input type="text" name="file_id">
    <br>
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <br>
    <input type="submit" value="Update file" name="update">
</form>

<?php if ($search): ?>
    <img src="data:image/jpg;base64,<?=$data?>" />';
<?php endif;?>

</body>
</html>