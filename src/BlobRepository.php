<?php

namespace MyITS\BlobRepository;

use GuzzleHttp\Client;
use InvalidArgumentException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class BlobRepository implements Contract
{
    protected $url = 'http://10.199.2.140:9999';

    protected $headers = [];

    protected $params = [];

    public $response;

    const VERSION = '0.0.1';

    private $client_id;

    private $client_secret;

    private $image;

    private $file;

    private $filename;

    private $file_ext;

    private $mime_content_type;

    private $metadata;

    private $xCode;

    public function __construct($client_id = null, $client_secret = null)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;

        $xCode = $this->getXcode();
        $this->setHeaders(['headers' => [
            'x-client-id' => $client_id,
            'x-code' => $xCode,
            'content-type' => 'application/json',
        ]]);

    }

    public function getXCode()
    {
        if (isset($_SESSION['blob_repository_xcode'])) {
            if ($this->isXCodeNotExpire()) {

            } else {
                $this->getAccessToken();
            }
            return $this->xCode;
        } else {
            $this->getAccessToken();
            return $this->xCode;
        }
    }

    public function setXCode($xCode)
    {
        $this->xCode = $xCode;
    }

    public function getAccessToken()
    {

        // curl --location --request POST "https://dev-my.its.ac.id/token" --header "Host: dev-my.its.ac.id"
        // --header "Content-Type: application/x-www-form-urlencoded" --data-urlencode "grant_type=client_credentials"
        // --data-urlencode "client_id=080507F5-DA58-45D2-B516-FD1BEFE7345B" --data-urlencode "client_secret="
        // OK cool - then let's create a new cURL resource handle

        $client = new Client();

        $headers = [
            'Host' => 'dev-my.its.ac.id',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $body = [
            'form_params' => [
                'grant_type' => 'client_credentials',
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
            ],
        ];
        $data = \array_merge($headers, $body);
        $response = $client->request('POST', 'https://dev-my.its.ac.id/token', $data);
        $response = json_decode($response->getBody()->getContents());
        if (isset($_SESSION)) {
            @session_start();
        }
        $_SESSION['blob_repository_xcode'] = $response->access_token;
        $_SESSION['blob_repository_token'] = $response;
        $this->setXCode($response->access_token);
        return $response->access_token;
    }

    /**
     * Wether xcode expired and need to refresh
     *
     * @return boolean
     */
    public function isXCodeNotExpire()
    {
        $client = new Client();

        $headers = [
            'x-client-id' => $this->client_id,
            'x-code' => $this->xCode,
            'Content-Type' => 'application/json',
        ];

        $data = $headers;
        $response = $client->request('GET', 'http://10.199.2.140:9999/d/files', $data);
        $response = json_decode($response->getBody()->getContents());

        if (isset($response['status'])) {
            if ($response['status'] == 'ERROR') {
                return false;
            }
            return true;
        }

    }

    /**
     * Check API version.
     *
     * @return string
     */
    public static function version()
    {
        return self::VERSION;
    }

    /**
     * If concrete instance UploadedFile, it should transform base64, either return url.
     *
     * @param $image
     * @return string
     */
    private function fileType($file)
    {
        if ($file['error'] == 0) {
            $this->filename = $file['name'];
            $file_ext = explode(".", $file['name']);
            $this->file_ext = end($file_ext);
            $this->mime_content_type = $file['type'];
            $this->metadata = [];
            return base64_encode(file_get_contents($file['tmp_name']));
        }
        return $file;
    }

    /**
     * Set headers.
     *
     * @param $headers
     * @return $this
     */
    public function setHeaders($headers)
    {
        $this->headers = $headers;

        return $this;
    }

    /**
     * If does not set headers, using default header, either return headers.
     *
     * @return array
     */
    private function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Set form params.
     *
     * @param $params
     * @return $this
     */
    public function setFormParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * If does not set form, using default form, either return form.
     *
     * @return array
     */
    private function getFormParams()
    {
        if (empty($this->params)) {
            return [
                'body' => json_encode([
                    'file_name' => $this->filename,
                    'file_ext' => $this->file_ext,
                    'mime_type' => $this->mime_content_type,
                    'binary_data_b64' => $this->file,
                ], JSON_UNESCAPED_SLASHES),
            ];
        }

        return $this->params;
    }

    private function setFile($file)
    {
        $this->file = $file;

        return $this;
    }

    private function setImage($image)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Main entrance point.
     *
     * @param $image
     * @return $this
     */
    public function upload($image)
    {
        $client = new Client();

        $uploadPath = '/c/store_file';

        $this->setImage($this->fileType($image));

        $data = array_merge($this->getHeaders(), $this->getFormParams());

        $response = $client->request('GET', $this->url . $uploadPath, $data);

        $this->setResponse(json_decode($response->getBody()->getContents()));
        return $this;
    }

    public function storeFile($file)
    {
        $client = new Client();

        $uploadPath = '/d/files';

        $this->setFile($this->fileType($file));
        ini_set("xdebug.var_display_max_children", -1);
        ini_set("xdebug.var_display_max_data", -1);
        ini_set("xdebug.var_display_max_depth", -1);
        $data = array_merge($this->getHeaders(), $this->getFormParams());
        var_dump($data);
        $response = $client->request('POST', $this->url . $uploadPath, $data);

        $this->setResponse(json_decode($response->getBody()->getContents()));
        return $this;
    }

    /**
     * get uploaded image etag.
     *
     * @return mixed
     */
    public function file_id()
    {
        return $this->response->info->file_id;
    }

    public function file_name()
    {
        return $this->response->info->file_name;
    }

    public function tag()
    {
        return $this->response->info->tag;
    }

    public function timestamp()
    {
        return $this->response->info->timestamp;
    }

    public function public_link()
    {
        return $this->response->info->public_link;
    }
    /**
     * get uploaded image size.
     *
     * @return mixed
     */
    public function filesize()
    {
        return $this->response->info->file_size;
    }

    /**
     * get uploaded image type.
     *
     * @return mixed
     */
    public function type()
    {
        return $this->response->info->type;
    }

    /**
     * get uploaded image width.
     *
     * @return mixed
     */
    public function width()
    {
        return $this->response->info->width;
    }

    /**
     * get uploaded image height.
     *
     * @return mixed
     */
    public function height()
    {
        return $this->response->info->height;
    }

    /**
     * get uploaded image usual parameters.
     *
     * @return mixed
     */
    public function usual()
    {
        return [
            'file_id' => $this->file_id(),
            'filesize' => $this->filesize(),
            'type' => $this->mime_content_type,
            'filename' => $this->filename,
            'tag' => $this->tag(),
            'timestamp' => $this->response->info->timestamp,
            'messate' => $this->response->message,
            'status' => $this->response->status,
            'publicLink' => $this->response->info->public_link,
        ];
    }

    private function setResponse($response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * @param $url
     * @param $size
     * @return string
     */
    public function size($url, $size)
    {
        if (!in_array($size, $this->size)) {
            throw new InvalidArgumentException("");
        }

        $delimiter = '';

        $image = explode('.', explode($delimiter, $url)[1]);

        return $delimiter . $image[0] . $size . '.' . $image[1];
    }

    /**
     * Get File from repo by etag
     *
     * @param string $etag
     * @return void
     */
    public function getFile($file_id = '')
    {
        $client = new Client();
        $searchPath = '/d/files/'.$file_id;
        
        $response = $client->request('GET', $this->url . $searchPath, $this->getHeaders());
        $this->setResponse(json_decode($response->getBody()->getContents()));
        return $this;
    }
}
