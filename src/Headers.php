<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

abstract class Headers extends Clients\Client
{
    public function __construct()
    {
        $headers = [];
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (substr($key, 0, 5) == 'HTTP_') {
                    $key = ucfirst(strtolower(str_replace('HTTP_', '', $key)));
                    if (strpos($key, '_') !== false) {
                        $ary = explode('_', $key);
                        foreach ($ary as $k => $v) {
                            $ary[$k] = ucfirst(strtolower($v));
                        }
                        $key = implode('-', $ary);
                    }
                    $headers[$key] = $value;
                }
            }
        }
        $headers = array_change_key_case($headers, CASE_LOWER);

        $this->headers = $headers;
    }

    public function setHeader($key, $value)
    {
        $this->headers[$this->normalizeKey($key)] = $value;
    }

    public function setHeaders(array $headers)
    {
        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        return $this;
    }

    public function update($key, $value)
    {
        if (!empty($this->get($this->normalizeKey($key)))) {
            $this->headers[$this->normalizeKey($key)] = $value;
        }
    }

    public function gets()
    {
        return $this->headers;
    }

    public function get($key)
    {
        return $this->headers[$this->normalizeKey($key)];
    }

    public function has($key)
    {
        $n_key = $this->normalizeKey($key);
        return (isset($this->headers[$n_key])) ? true : false;
    }

    public function remove($key)
    {
        unset($this->headers[$key]);
    }

    public function normalizeKey($key)
    {
        $key = strtr(strtolower($key), '_', '-');

        return $key;
    }

    public function send($code = null, array $headers = null)
    {
        if ($code !== null) {
            $this->withStatus($code);
        }
        if ($headers !== null) {
            $this->setHeaders($headers);
        }

        $body = $this->body;

        if (array_key_exists('Content-Encoding', $this->headers)) {
            $body = self::encodeBody($body, $this->headers['Content-Encoding']);
            $this->headers['Content-Length'] = strlen($body);
        }

        $this->sendHeaders();
        echo $body;
    }

    public function sendHeaders()
    {
        if (headers_sent()) {
            throw new \Exception('The headers have already been sent.');
        }

        header("HTTP/{$this->version} {$this->code} {$this->reasonPhrase}");
        foreach ($this->headers as $name => $value) {
            if (!is_array($value)) {
                header($name.': '.$value);
            } else {
                foreach ($value as $k => $v) {
                    header($k.': '.$v);
                }
            }
        }
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
}
