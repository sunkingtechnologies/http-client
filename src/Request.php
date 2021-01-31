<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

class Request extends Uri
{
    public function __construct()
    {
        parent::__construct();
        $this->get = (isset($_GET)) ? $_GET : [];
        $this->post = (isset($_POST)) ? $_POST : [];
        $this->files = (isset($_FILES)) ? $_FILES : [];
        $this->cookie = (isset($_COOKIE)) ? $_COOKIE : [];
        $this->server = (isset($_SERVER)) ? $_SERVER : [];
        $this->env = (isset($_ENV)) ? $_ENV : [];

        if ($this->getRequestMethod()) {
            $this->parseData();
        }
    }

    public function isMethod($method)
    {
        return $this->getRequestMethod() === $method;
    }

    public function hasFiles()
    {
        return count($this->files) > 0;
    }

    public function isGet()
    {
        return $this->isMethod('GET');
    }

    public function isHead()
    {
        return $this->isMethod('HEAD');
    }

    public function isPost()
    {
        return $this->isMethod('POST');
    }

    public function isPut()
    {
        return $this->isMethod('PUT');
    }

    public function isDelete()
    {
        return $this->isMethod('DELETE');
    }

    public function isTrace()
    {
        return $this->isMethod('TRACE');
    }

    public function isOptions()
    {
        return $this->isMethod('OPTIONS');
    }

    public function isConnect()
    {
        return $this->isMethod('CONNECT');
    }

    public function isPatch()
    {
        return $this->isMethod('PATCH');
    }

    public function isSecure()
    {
        $https = $this->gethttps();
        $server_port = $this->getServerPort();
        return ($https || $server_port == '443') ? true : false;
    }

    public function isXhr()
    {
        return strtolower($this->getHeader('x-requested-with')) === 'xmlhttprequest';
    }

    public function getQuery($key = null)
    {
        if ($key === null) {
            return $this->get;
        } else {
            return (isset($this->get[$key])) ? $this->get[$key] : null;
        }
    }

    public function getPost($key = null)
    {
        if ($key === null) {
            return $this->post;
        } else {
            return (isset($this->post[$key])) ? $this->post[$key] : null;
        }
    }

    public function getFiles($key = null)
    {
        if ($key === null) {
            return $this->files;
        } else {
            return (isset($this->files[$key])) ? $this->files[$key] : null;
        }
    }

    public function getPut($key = null)
    {
        if ($key === null) {
            return $this->put;
        } else {
            return (isset($this->put[$key])) ? $this->put[$key] : null;
        }
    }

    public function getPatch($key = null)
    {
        if ($key === null) {
            return $this->patch;
        } else {
            return (isset($this->patch[$key])) ? $this->patch[$key] : null;
        }
    }

    public function getDelete($key = null)
    {
        if ($key === null) {
            return $this->delete;
        } else {
            return (isset($this->delete[$key])) ? $this->delete[$key] : null;
        }
    }

    public function getCookie($key = null)
    {
        if ($key === null) {
            return $this->cookie;
        } else {
            return (isset($this->cookie[$key])) ? $this->cookie[$key] : null;
        }
    }

    public function getServer($key = null)
    {
        if ($key === null) {
            return $this->server;
        } else {
            return (isset($this->server[$key])) ? $this->server[$key] : null;
        }
    }

    public function getEnv($key = null)
    {
        if ($key === null) {
            return $this->env;
        } else {
            return (isset($this->env[$key])) ? $this->env[$key] : null;
        }
    }

    public function getParsedData($key = null)
    {
        $result = null;

        if ($this->parsedData !== null && is_array($this->parsedData)) {
            if (null === $key) {
                $result = $this->parsedData;
            } else {
                $result = (isset($this->parsedData[$key])) ? $this->parsedData[$key] : null;
            }
        }

        return $result;
    }

    public function getRawData()
    {
        return $this->rawData;
    }

    protected function parseData()
    {
        if (strtoupper($this->getRequestMethod()) == 'GET') {
            $this->rawData = ($this->getQueryString()) ? rawurldecode($this->getQueryString()) : null;
        } else {
            $input = fopen('php://input', 'r');
            while ($data = fread($input, 1024)) {
                $this->rawData .= $data;
            }
        }

        // If the content-type is JSON
        if ($this->getQueryString() && stripos($this->getQueryString(), 'json') !== false) {
            $this->parsedData = json_decode($this->rawData, true);
            // Else, if the content-type is XML
        } elseif ($this->getContentType() && stripos($this->getContentType(), 'xml') !== false) {
            $matches = [];
            preg_match_all('/<!\[cdata\[(.*?)\]\]>/is', $this->rawData, $matches);

            foreach ($matches[0] as $match) {
                $strip = str_replace(
                    ['<![CDATA[', ']]>', '<', '>'],
                    ['', '', '&lt;', '&gt;'],
                    $match
                );
                $this->rawData = str_replace($match, $strip, $this->rawData);
            }

            $this->parsedData = json_decode(json_encode((array) simplexml_load_string($this->rawData)), true);
            // Else, default to a regular URL-encoded string
        } else {
            switch (strtoupper($this->getRequestMethod())) {
                case 'GET':
                    $this->parsedData = $this->get;
                    break;

                case 'POST':
                    $this->parsedData = $this->post;
                    break;
                default:
                    if ($this->getContentType() && strtolower($this->getContentType()) == 'application/x-www-form-urlencoded') {
                        parse_str($this->rawData, $this->parsedData);
                    }
            }
        }
        switch (strtoupper($this->getRequestMethod())) {
            case 'PUT':
                $this->put = $this->parsedData;
                break;

            case 'PATCH':
                $this->patch = $this->parsedData;
                break;

            case 'DELETE':
                $this->delete = $this->parsedData;
                break;
        }
    }
}
