<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

class Message extends HTTP
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getProtocolVersion()
    {
        return $this->version;
    }

    public function withProtocolVersion($version)
    {
        if (!isset(self::$validProtocolVersions[$version])) {
            throw new \InvalidArgumentException('Invalid HTTP version. Must be one of: '.implode(', ', array_keys(self::$validProtocolVersions)), 500);
        }
        $this->version = $version;

        return $this;
    }

    public function getHeaders()
    {
        return $this->gets();
    }

    public function hasHeader($name)
    {
        return $this->has($name);
    }

    public function getHeader($name)
    {
        return $this->get($name);
    }

    public function getHeaderLine($name)
    {
        return implode(',', $this->get($name));
    }

    public function withHeader($name, $value)
    {
        $this->update($name, $value);

        return $this;
    }

    public function withAddedHeader($name, $value)
    {
        $this->setHeader($name, $value);

        return $this;
    }

    public function withoutHeader($name)
    {
        $this->remove($name);

        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function withBody($body)
    {
        $this->body = $body;

        return $this;
    }
}
