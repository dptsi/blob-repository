<?php
error_reporting(1);
require_once 'vendor/autoload.php';

use Dptsi\BlobRepository\BlobRepository;
$blobUpload = new BlobRepository('https://my.its.ac.id', '786EDC44-1146-424C-A46A-0430763ABA64', 'e6a4aa311cadd91b61323643');

if (isset($_POST["submit"])) {
    $d = $blobUpload->storeFile($_FILES['fileToUpload']);
    var_dump($d->usual());
}

if (isset($_POST["update"]) && isset($_POST["file_id"])) {
    $d = $blobUpload->updateFile($_FILES['fileToUpload'], $_POST["file_id"]);
    var_dump($d->usual());
}

if (isset($_POST["search"]) && isset($_POST["file_id"])) {
    $d = $blobUpload->getFile($_POST["file_id"]);
    var_dump($d);
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
<h3>Search File ID</h3>

<form action="" method="post" enctype="multipart/form-data">
    File ID: <input type="text" name="file_id">         
    <input type="submit" value="Search" name="search">
</form>
    <?php $publicLink = ''; ?>
    <img src="data:image/jpg;base64,<?=$d->response->data?>" />';

</body>
</html>