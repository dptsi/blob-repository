ITS Blob Repository (DEV)
===========

Library for connecting to blob repo


# Requirements #
 1. PHP 5.4 ++
 2. guzzle
 
 example is in test-upload.php
 
 

# Instalation #

    add composer.json
        "myits/blob-repository": "dev-master"

    after require section add this
        "repositories": [
            {
                "type": "vcs",
                "url": "https://github.com/dptsi/blob-repository.git"
            }
        ]

# Usage #
    Instalation
    <?php
    require './vendor/autoload.php';
    $blobUpload = new BlobRepository('SSO Provider', 'SSO Client ID', 'SSO Secret', 'Alamat Storage API (Kosong jika prod)');
    
    Get File 
    $file  = $blobUpload->getFile($file_id);
    
    
    Store File 
    $file Upload = $blobUpload->storeFile($_FILES['fileToUpload']);
    
    Delete File 
    $file Upload = $blobUpload->deleteFile($file_id);
    
    Update File 
    $file Upload = $blobUpload->updateFile($_FILES['fileToUpload'], $file_id);

    Methods

    file_id()
    file_name()
    tag()
    timestamp()
    public_link()
    
    To get responses from server access $file->response
 
