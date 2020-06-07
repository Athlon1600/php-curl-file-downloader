# Download large files using PHP and cURL

There's too many code snippets on the Internet on how to do this, but not enough libraries. So I made this.

```php
<?php

use Curl\Client;
use CurlDownloader\CurlDownloader;

$browser = new Client();
$downloader = new CurlDownloader($browser);

$downloader->download("https://download.ccleaner.com/cctrialsetup.exe", function ($filename) {
    return './2020-06-07-' . $filename;
});
```

## Installation

```bash
composer require athlon1600/php-curl-file-downloader
```

### Links

- https://demo.borland.com/testsite/download_testpage.php

