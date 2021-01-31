<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

class Uri extends Message
{
    public function __construct()
    {
        parent::__construct();
    }

    public function setUri($scheme, $host, $port = null, $path = '/', $query = '', $fragment = '', $user = '', $password = '')
    {
        $this->scheme = $this->filterScheme($scheme);
        $this->host = $host;
        $this->port = $this->filterPort($port);
        $this->path = empty($path) ? '/' : $this->filterQuery($path);
        $this->query = $this->filterQuery($query);
        $this->fragment = $this->filterQuery($fragment);
        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    public function createFormUrl($url)
    {
        $urlParts = parse_url($url);

        $scheme = isset($urlParts['scheme']) ? $urlParts['scheme'] : '';
        $user = isset($urlParts['user']) ? $urlParts['user'] : '';
        $password = isset($urlParts['pass']) ? $urlParts['pass'] : '';
        $host = isset($urlParts['host']) ? $urlParts['host'] : '';
        $port = isset($urlParts['port']) ? $urlParts['port'] : null;
        $path = isset($urlParts['path']) ? $urlParts['path'] : '';
        $query = isset($urlParts['query']) ? $urlParts['query'] : '';
        $fragment = isset($urlParts['fragment']) ? $urlParts['fragment'] : '';

        $this->setUri($scheme, $host, $port, $path, $query, $fragment, $user, $password);

        return $this;
    }

    public function filterScheme($scheme)
    {
        $valids = [
            ''      => true,
            'http'  => true,
            'https' => true,
        ];
        $scheme = str_replace('://', '', strtolower((string) $scheme));
        if (!isset($valids[$scheme])) {
            throw new \InvalidArgumentException('Uri scheme must be one of: "", "https", "http"');
        }

        return $scheme;
    }

    public function filterPort($port)
    {
        if (is_null($port) || is_int($port)) {
            return $port;
        }

        throw new \InvalidArgumentException('Uri port much be type int');
    }

    public function filterQuery($query)
    {
        return rawurlencode($query);
    }

    public function getScheme()
    {
        return $this->scheme;
    }

    public function getDeterminedScheme()
    {
        return $this->isSecure() ? 'https' : 'http';
    }

    public function getAuthority()
    {
        return ($this->getUserInfo() !== '') ? $this->getUserInfo(). '@' :
            (''.$this->getHost().($this->getPort() !== '') ? ':'.$this->getPort() : '');
    }

    public function getUserInfo()
    {
        return ($this->user !== '') ? $this->user : (''.($this->password !== '') ? ':'.$this->password : '');
    }

    public function getHost()
    {
        return $this->host;
    }

    public function getPath()
    {
        return $this->path;
    }

    public function getPort()
    {
        return $this->port;
    }

    public function getQuery()
    {
        return $this->query;
    }

    public function getFragment()
    {
        return $this->fragment;
    }

    public function getBasePath()
    {
        return $this->basePath;
    }

    public function withUserInfo($user, $password = null)
    {
        $this->user = $user;
        $this->password = $password;

        return $this;
    }

    public function withHost($host)
    {
        $this->host = $host;

        return $this;
    }

    public function withPath($path)
    {
        // if the path is absolute, then clear basePath
        if (substr($path, 0, 1) == '/') {
            $this->basePath = '';
        }
        $this->path = $this->filterQuery($path);

        return $this;
    }

    public function withPort($port)
    {
        $this->port = $this->filterPort($port);

        return $this;
    }

    public function withQuery($query)
    {
        $this->query = $this->filterQuery(ltrim($query, '?'));

        return $this;
    }

    public function withFragment($fragment)
    {
        $this->fragment = $this->filterQuery(ltrim($fragment, '#'));

        return $this;
    }

    public function withBasePath($basePath)
    {
        $this->basePath = $this->filterQuery($basePath);

        return $this;
    }

    public function __toString()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $basePath = $this->getBasePath();
        $path = $this->getPath();
        $query = $this->getQuery();
        $fragment = $this->getFragment();

        $path = $basePath.'/'.ltrim($path, '/');

        return ($scheme !== '' ? $scheme.':' : '')
            .($authority !== '' ? '//'.$authority : '')
            .$path
            .($query !== '' ? '?'.$query : '')
            .($fragment !== '' ? '#'.$fragment : '');
    }

    public function getBaseUrl()
    {
        $scheme = $this->getScheme();
        $authority = $this->getAuthority();
        $basePath = $this->getBasePath();

        if ($authority !== '' && substr($basePath, 0, 1) !== '/') {
            $basePath = $basePath.'/'.$basePath;
        }

        return ($scheme !== '' ? $scheme.':' : '')
            .($authority ? '//'.$authority : '')
            .rtrim($basePath, '/');
    }
}
