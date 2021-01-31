<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

class Redirect extends HTTP
{

    public function __construct($url, $code = '302', $version = '1.1')
    {
        parent::__construct();
        if (headers_sent()) {
            throw new \Exception('The headers have already been sent.');
        }

        if (!array_key_exists($code, self::$responseCodes)) {
            throw new \Exception('The header code '.$code.' is not allowed.');
        }

        header("HTTP/{$version} {$code} ".self::$responseCodes[$code]);
        header("Location: {$url}");
    }
}
