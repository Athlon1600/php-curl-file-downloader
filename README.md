[![Supported PHP Versions](https://img.shields.io/badge/PHP-7.3,%208.0,%208.1,%208.2-blue)](https://github.com/Athlon1600/php-curl-file-downloader)
![GitHub Workflow Status (with event)](https://img.shields.io/github/actions/workflow/status/Athlon1600/php-curl-file-downloader/build.yml)
![](https://img.shields.io/github/last-commit/Athlon1600/php-curl-file-downloader.svg)
![Packagist Downloads (custom server)](https://img.shields.io/packagist/dm/Athlon1600/php-curl-file-downloader)

# Download large files using PHP and cURL

There's too many code snippets on the Internet on how to do this, but not enough libraries. 

This will allow you to download files of any size using cURL without ever running out of memory. That's it.

```php
<?php

use Curl\Client;
use CurlDownloader\CurlDownloader;

$browser = new Client();
$downloader = new CurlDownloader($browser);

$response = $downloader->download("https://download.ccleaner.com/cctrialsetup.exe", function ($filename) {
    return './2020-06-07-' . $filename;
});

if ($response->status == 200) {
    // 28,851,928 bytes downloaded in 20.041231 seconds
    echo number_format($response->info->size_download) . ' bytes downloaded in ' . $response->info->total_time . ' seconds';
}
```

It will automatically guess filename of the resource being downloaded (just like web-browsers do it!) with an option to override it if needed.

## Installation

```bash
composer require athlon1600/php-curl-file-downloader "^1.0"
```

### Links

- https://github.com/Athlon1600/php-curl-client
- https://demo.borland.com/testsite/download_testpage.php

