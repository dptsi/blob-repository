<?php
error_reporting(1);
require_once 'vendor/autoload.php';

use MyITS\BlobRepository\BlobRepository;
$blobUpload = new BlobRepository('', '', '', '');

if (isset($_POST["submit"])) {
    $d = $blobUpload->upload($_FILES['fileToUpload']);
    var_dump($d);
}

$search = true;
if ($search) {
    $d = $blobUpload->getFile('34d04c43ea312eee5b723cdd725fec28-1');
    $data = $d->response->data;
}

?>
<!DOCTYPE html>
<html>
<body>

<form action="" method="post" enctype="multipart/form-data">
    Select image to upload:
    <input type="file" name="fileToUpload" id="fileToUpload">
    <input type="submit" value="Upload Image" name="submit">
</form>

<?php if ($search): ?>
    <img src="data:image/jpg;base64,<?=$data?>" />';
<?php endif;?>

</body>
</html>