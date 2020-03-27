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
                "url": "https://git.its.ac.id/umar/blob-repository.git"
            }
        ]

# Usage #
    Instalation
    <?php
    require './vendor/autoload.php';
    $blobUpload = new BlobRepository('https://my.its.ac.id','080507F5-DA58-45D2-B516-FD1BEFE7345B', '6vi17be2fn0o0o8gw4g84c4g');


    Upload 
    $file Upload = $blobUpload->storeFile($_FILES['fileToUpload']);

    
    Get File 
    $file  = $blobUpload->getFile($file_id');

    Methods

    file_id()
    file_name()
    tag()
    timestamp()
    public_link()
    
    To get responses from server access $file->response
 