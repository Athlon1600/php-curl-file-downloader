<?php

namespace CurlDownloader\Tests;

use Curl\Client;
use CurlDownloader\CurlDownloader;
use PHPUnit\Framework\TestCase;

class DownloadTest extends TestCase
{
    /** @var CurlDownloader */
    protected $client;

    protected function setUp()
    {
        $this->client = new CurlDownloader(new Client());
    }

    public function test_download_directly()
    {
        $this->client->download("https://demo.borland.com/testsite/downloads/Small.zip", function ($filename) {
            return './' . $filename;
        });

        $this->assertTrue(file_exists('./Small.zip'));

        unlink('./Small.zip');
    }

    public function test_download_content_disposition()
    {
        $this->client->download("https://demo.borland.com/testsite/downloads/downloadfile.php?file=Data1KB.dat&cd=attachment+filename", function ($filename) {
            return './' . $filename;
        });

        $this->assertTrue(file_exists('./Data1KB.dat'));

        unlink('./Data1KB.dat');
    }

    public function test_download_content_disposition_github()
    {
        $this->client->download("https://github.com/guzzle/guzzle/releases/download/6.5.4/guzzle.zip", function ($filename) {
            return './' . $filename;
        });

        $this->assertTrue(file_exists('./guzzle.zip'));

        unlink('./guzzle.zip');
    }

    public function test_download_content_disposition_custom_filename()
    {
        $this->client->download("https://demo.borland.com/testsite/downloads/downloadfile.php?file=Data1KB.dat&cd=attachment+filename", function ($filename) {
            return './2020-06-07-' . $filename;
        });

        $this->assertTrue(file_exists('./2020-06-07-Data1KB.dat'));

        unlink('./2020-06-07-Data1KB.dat');
    }
}