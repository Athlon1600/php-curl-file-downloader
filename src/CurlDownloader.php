<?php

namespace CurlDownloader;

use Curl\Client;

class CurlDownloader
{
    /** @var Client */
    private $client;

    // Timeout after 10 minutes.
    protected $max_timeout = 600;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    protected function createTempFile()
    {
        return tempnam(sys_get_temp_dir(), uniqid());
    }

    protected function getFilenameFromUrl($url)
    {
        $url_path = parse_url($url, PHP_URL_PATH);
        return basename($url_path);
    }

    /**
     * @param $url
     * @param $destination
     * @return \Curl\Response
     */
    public function download($url, $destination)
    {
        $handler = new HeaderHandler();

        // Will download file to temp for now
        $temp_filename = $this->createTempFile();

        $handle = fopen($temp_filename, 'w+');

        $response = $this->client->request('GET', $url, [], [], [
            CURLOPT_FILE => $handle,
            CURLOPT_HEADERFUNCTION => $handler->callback(),
            CURLOPT_TIMEOUT => $this->max_timeout
        ]);

        if ($response->info->http_code === 200) {
            $filename = $handler->getContentDispositionFilename();

            if (empty($filename)) {
                $filename = $this->getFilenameFromUrl($response->info->url);
            }

            $save_to = call_user_func($destination, $filename);

            rename($temp_filename, $save_to);
        }

        @fclose($handle);
        @unlink($temp_filename);

        return $response;
    }
}
