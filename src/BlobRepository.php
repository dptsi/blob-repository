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

    private $filename;

    private $metadata;

    public function __construct($client_id, $client_secret)
    {
        $this->client_id = $client_id;
        $this->client_secret = $client_secret;
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
    private function fileType($image)
    {
        var_dump($image);
        if ($image['error'] == 0) {
            $this->filename = $image['name'];
            $this->metadata = [];
            return base64_encode(file_get_contents($image['tmp_name']));
        }

        return $image;
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
        if (empty($this->headers)) {
            return [
                'headers' => [
                    'x-client-id' => 'kucinglucu',
                    'x-code' => '1234_015f23075cc0816b8f7f30d2ba8a7641',
                    'content-type' => 'application/json',
                ],
            ];
        }

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
                    'nama' => $this->filename,
                    'binary_data_b64' => $this->image,
                    'metadata' => $this->metadata,
                ]),
            ];
        }

        return $this->params;
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

    /**
     * get uploaded image etag.
     *
     * @return mixed
     */
    public function etag()
    {
        return $this->response->info->etag;
    }

    /**
     * get uploaded image size.
     *
     * @return mixed
     */
    public function filesize()
    {
        return $this->response->info->size;
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
            'etag' => $this->etag(),
            'filesize' => $this->filesize(),
            'type' => $this->type(),
            'filename' => $this->filename,
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
    public function getFile($etag = '')
    {
        $client = new Client();
        $searchPath = '/c/get_file';
        $data = [
            'body' => json_encode([
                'tag' => $etag,
            ]),
        ];
        $data = array_merge($this->getHeaders(), $data);
        $response = $client->request('GET', $this->url . $searchPath, $data);
        $this->setResponse(json_decode($response->getBody()->getContents()));
        return $this;
    }
}
