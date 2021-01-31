<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

class Response extends Message
{

    public function __construct(array $config = [])
    {
        parent::__construct();
        // Check for config values and set defaults
        if (!isset($config['version'])) {
            $config['version'] = '1.1';
        }
        if (!isset($config['code'])) {
            $config['code'] = 200;
        }

        $this->setVersion($config['version'])
            ->withStatus($config['code']);

        if (!isset($config['reasonPhrase'])) {
            $config['reasonPhrase'] = self::$responseCodes[$config['code']];
        }
        if (!isset($config['headers']) || (isset($config['headers']) && !is_array($config['headers']))) {
            $config['headers'] = ['Content-Type' => 'text/html'];
        }
        if (!isset($config['body'])) {
            $config['body'] = null;
        }

        $this->setReasonPhrase($config['reasonPhrase'])
            ->setHeaders($config['headers'])
            ->setBody($config['body']);
    }

    public static function getMessageFromCode($code)
    {
        if (!array_key_exists($code, self::$responseCodes)) {
            throw new \Exception('The header code '.$code.' is not valid.');
        }

        return self::$responseCodes[$code];
    }

    public static function encodeBody($body, $encode = 'gzip')
    {
        switch ($encode) {
            // GZIP compression
            case 'gzip':
                if (!function_exists('gzencode')) {
                    throw new \Exception('Gzip compression is not available.');
                }
                $encodedBody = gzencode($body);
                break;

            // Deflate compression
            case 'deflate':
                if (!function_exists('gzdeflate')) {
                    throw new \Exception('Deflate compression is not available.');
                }
                $encodedBody = gzdeflate($body);
                break;

            // Unknown compression
            default:
                $encodedBody = $body;

        }

        return $encodedBody;
    }

    public static function decodeBody($body, $decode = 'gzip')
    {
        switch ($decode) {
            // GZIP compression
            case 'gzip':
                if (!function_exists('gzinflate')) {
                    throw new \Exception('Gzip compression is not available.');
                }
                $decodedBody = gzinflate(substr($body, 10));
                break;

            // Deflate compression
            case 'deflate':
                if (!function_exists('gzinflate')) {
                    throw new \Exception('Deflate compression is not available.');
                }
                $zlibHeader = unpack('n', substr($body, 0, 2));
                $decodedBody = ($zlibHeader[1] % 31 == 0) ? gzuncompress($body) : gzinflate($body);
                break;

            // Unknown compression
            default:
                $decodedBody = $body;

        }

        return $decodedBody;
    }

    public function isEmpty()
    {
        $status_code = $this->getStatusCode();
        return in_array($status_code, array(204, 205, 304));
    }

    public function isOk()
    {
        return $this->getStatusCode() === 200;
    }

    public function isSuccessful()
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }

    public function isRedirect()
    {
        return in_array($this->getStatusCode(), [301, 302, 303, 307, 308]);
    }

    public function isForbidden()
    {
        return $this->getStatusCode() === 403;
    }

    public function isNotFound()
    {
        return $this->getStatusCode() === 404;
    }

    public function isClientError()
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }

    public function isServertusCode()
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }

    public function getHeadersAsString($status = true, $eol = "\n")
    {
        $headers = '';

        if ($status) {
            $headers = "HTTP/{$this->version} {$this->code} {$this->reasonPhrase}{$eol}";
        }

        foreach ($this->headers as $name => $value) {
            $headers .= "{$name}: {$value}{$eol}";
        }

        return $headers;
    }

    public function setReasonPhrase($reasonPhrase = '')
    {
        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    public function setVersion($version = 1.1)
    {
        $this->withProtocolVersion($version);

        return $this;
    }

    public function getStatusCode()
    {
        return $this->code;
    }

    public function withStatus($code = 200)
    {
        if (!array_key_exists($code, self::$responseCodes)) {
            throw new \Exception('That header code '.$code.' is not allowed.');
        }

        $this->code = $code;
        $this->reasonPhrase = self::$responseCodes[$code];

        return $this;
    }

    public function setBody($body = null)
    {
        $this->withBody($body);

        return $this;
    }

    public function setSslHeaders()
    {
        $this->setHeader('Expires', 0);
        $this->setHeader('Cache-Control', 'private, must-revalidate');
        $this->setHeader('Pragma', 'cache');

        return $this;
    }

    public function sendAndExit($code = null, array $headers = null)
    {
        $this->send($code, $headers);
        exit();
    }

    public function __toString()
    {
        $body = $this->body;

        if (array_key_exists('Content-Encoding', $this->headers)) {
            $body = self::encodeBody($body, $this->headers['Content-Encoding']);
            $this->headers['Content-Length'] = strlen($body);
        }

        return $this->getHeadersAsString()."\n".$body;
    }
}
