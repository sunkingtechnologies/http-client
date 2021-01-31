<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

abstract class HTTP extends Headers
{
    use StatusCode,ValidProtocolVersions;
    protected $requestUri = null;
    protected $segments = [];
    protected $basePath = null;
    protected $headers = [];
    protected $rawData = null;
    protected $parsedData = null;
    protected $get = [];
    protected $post = [];
    protected $files = [];
    protected $put = [];
    protected $patch = [];
    protected $delete = [];
    protected $cookie = [];
    protected $server = [];
    protected $env = [];
    protected $version = '1.1';
    protected $code = null;
    protected $reasonPhrase = null;
    protected $scheme = '';
    protected $user = '';
    protected $password = '';
    protected $host = '';
    protected $port;
    protected $path = '';
    protected $query = '';
    protected $fragment = '';
    protected $body = null;

    public function __construct()
    {
        parent::__construct();
    }

    public function getRequestMethod()
    {
        return (isset($_SERVER['REQUEST_METHOD'])) ? $_SERVER['REQUEST_METHOD'] : false;
    }

    public function getRequestUrl()
    {
        return (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : false;
    }

    public function getContentType()
    {
        return (isset($_SERVER['CONTENT_TYPE'])) ? $_SERVER['CONTENT_TYPE'] : false;
    }

    public function getQueryString()
    {
        return (isset($_SERVER['QUERY_STRING'])) ? $_SERVER['QUERY_STRING'] : false;
    }

    public function getServerPort()
    {
        return (isset($_SERVER['SERVER_PORT'])) ? $_SERVER['SERVER_PORT'] : false;
    }

    public function getDocumentRoot()
    {
        return (isset($_SERVER['DOCUMENT_ROOT'])) ? $_SERVER['DOCUMENT_ROOT'] : false;
    }

    public function getHost()
    {
        return (isset($_SERVER['HTTP_HOST'])) ? $_SERVER['HTTP_HOST'] : false;
    }

    public function getServerName()
    {
        return (isset($_SERVER['SERVER_NAME'])) ? $_SERVER['SERVER_NAME'] : false;
    }

    public function gethttp()
    {
        return (isset($_SERVER['HTTP'])) ? $_SERVER['HTTP'] : false;
    }

    public function gethttps()
    {
        return (isset($_SERVER['HTTPS'])) ? $_SERVER['HTTPS'] : false;
    }

    public function getSelf()
    {
        return (isset($_SERVER['PHP_SELF'])) ? $_SERVER['PHP_SELF'] : false;
    }

    public function getReference()
    {
        return (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : false;
    }
}
