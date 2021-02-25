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

    protected function getPathFromUrl($url)
    {
        return parse_url($url, PHP_URL_PATH);
    }

    protected function getFilenameFromUrl($url)
    {
        // equivalent to: pathinfo with PATHINFO_FILENAME
        return basename($this->getPathFromUrl($url));
    }

    protected function getExtensionFromUrl($url)
    {
        return pathinfo($this->getFilenameFromUrl($url), PATHINFO_EXTENSION);
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

        // TODO: refactor this whole filename logic into its own class
        if ($response->info->http_code === 200) {
            $filename = $handler->getContentDispositionFilename();

            if (empty($filename)) {
                $url = $response->info->url;

                $filename = $this->getFilenameFromUrl($url);

                $extension_from_url = $this->getExtensionFromUrl($url);
                $extension_from_content_type = ContentTypes::getExtension($handler->getContentType());

                // E.g: https://www.google.com/
                if (empty($filename)) {
                    $filename = 'index.' . ($extension_from_content_type ? $extension_from_content_type : 'html');
                } else {

                    // in case filename in url is like `videoplayback` with `content-type: video/mp4`
                    if (empty($extension_from_url) && $extension_from_content_type) {
                        $filename = ($filename . '.' . $extension_from_content_type);
                    }
                }
            }

            $save_to = call_user_func($destination, $filename);

            rename($temp_filename, $save_to);
        }

        @fclose($handle);
        @unlink($temp_filename);

        return $response;
    }
}
