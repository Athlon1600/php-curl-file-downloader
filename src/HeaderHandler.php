<?php

namespace CurlDownloader;

class HeaderHandler
{
    protected $headers = array();

    /** @var callable */
    protected $callback;

    // Thanks Drupal!
    // const REQUEST_HEADER_FILENAME_REGEX = '@\\bfilename(?<star>\\*?)=\\"(?<filename>.+)\\"@';
    const REQUEST_HEADER_FILENAME_REGEX = '/filename\s*=\s*["\']*(?<filename>[^"\']+)/';

    public function callback()
    {
        $oThis = $this;

        $headers = array();
        $first_line_sent = false;

        return function ($ch, $data) use ($oThis, &$first_line_sent, &$headers) {
            $line = trim($data);

            if ($first_line_sent == false) {
                $first_line_sent = true;
            } elseif ($line === '') {
                $oThis->sendHeaders();
            } else {

                $parts = explode(':', $line, 2);

                // Despite that headers may be retrieved case-insensitively, the original case MUST be preserved by the implementation
                // Non-conforming HTTP applications may depend on a certain case,
                // so it is useful for a user to be able to dictate the case of the HTTP headers when creating a request or response.

                // TODO:
                // Multiple message-header fields with the same field-name may be present in a message
                // if and only if the entire field-value for that header field is defined as a comma-separated list
                $oThis->headers[trim($parts[0])] = isset($parts[1]) ? trim($parts[1]) : null;
            }

            return strlen($data);
        };
    }

    protected function sendHeaders()
    {
        if (is_callable($this->callback)) {
            call_user_func($this->callback, $this);
        }
    }

    /**
     * @param callable $callback
     */
    public function onHeadersReceived($callback)
    {
        $this->callback = $callback;
    }

    // While header names are not case-sensitive, getHeaders() will preserve the exact case in which headers were originally specified.
    public function getHeaders()
    {
        return $this->headers;
    }

    public function getContentDispositionFilename()
    {
        $normalized = array_change_key_case($this->headers, CASE_LOWER);
        $header = isset($normalized['content-disposition']) ? $normalized['content-disposition'] : null;

        if ($header && preg_match(static::REQUEST_HEADER_FILENAME_REGEX, $header, $matches)) {
            return $matches['filename'];
        }

        return null;
    }

    public function getContentType()
    {
        $normalized = array_change_key_case($this->headers, CASE_LOWER);
        return isset($normalized['content-type']) ? $normalized['content-type'] : null;
    }
}