<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing\Clients;

use SunKing\Response;

abstract class BaseClient
{
    protected $resource = '';
    protected $url = '';
    protected $method = '';
    protected $fields = [];
    protected $query = '';
    protected $requestHeaders = [];
    protected $version = '';
    protected $code;
    protected $message = '';
    protected $response = '';
    protected $responseHeader = '';
    protected $responseHeaders = [];
    protected $body = '';
    protected $options = [];

    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setMethod($method)
    {
        $valid = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS', 'TRACE', 'CONNECT'];
        $method = strtoupper($method);

        if (!in_array($method, $valid)) {
            throw new \Exception('Error: That request method is not valid.');
        }
        $this->method = $method;

        return $this;
    }

    function getMethod()
    {
        return $this->method;
    }

    public function setField($name, $value)
    {
        $this->fields[$name] = $value;

        return $this;
    }

    public function setFields(array $fields)
    {
        foreach ($fields as $name => $value) {
            $this->setField($name, $value);
        }

        $this->prepareQuery();

        return $this;
    }

    public function getField($name)
    {
        return (isset($this->fields[$name])) ? $this->fields[$name] : false;
    }

    public function getFields($name)
    {
        if (isset($this->fields[$name])) {
            unset($this->fields[$name]);
        }

        $this->prepareQuery();

        return $this;
    }

    public function prepareQuery()
    {
        $this->query = http_build_query($this->fields);

        return $this;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getQueryLength($mb = true)
    {
        return ($mb) ? mb_strlen($this->query) : strlen($this->query);
    }

    public function setRequestHeader($name, $value)
    {
        $this->requestHeaders[$name] = $value;

        return $this;
    }

    public function setRequestHeaders(array $headers)
    {
        $this->requestHeaders = $headers;

        return $this;
    }

    public function getRequestHeader($name)
    {
        return (isset($this->requestHeaders[$name])) ? $this->requestHeaders[$name] : null;
    }

    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    public function hasRequestHeaders()
    {
        return count($this->requestHeaders) > 0;
    }

    public function getResponseHeader($name)
    {
        return (isset($this->responseHeaders[$name])) ? $this->responseHeaders[$name] : null;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function hasResponseHeaders()
    {
        return count($this->responseHeaders) > 0;
    }

    public function getRawResponseHeader()
    {
        return $this->responseHeader;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getHttpVersion()
    {
        return $this->version;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function hasResource()
    {
        return is_resource($this->resource);
    }

    public function getResource()
    {
        return $this->resource;
    }

    public function decodeBody()
    {
        $this->body = Response::decodeBody($this->body, $this->responseHeaders['Content-Encoding']);
    }

    public function throwError($error)
    {
        throw new \Exception($error);
    }

    abstract public function open();
    abstract public function send();
    abstract public function close();
}
