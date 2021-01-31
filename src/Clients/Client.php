<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing\Clients;

use SunKing\CURL;

class Client
{

    public function curl($url, $method = 'GET', array $options = null)
    {
        try {
            return new CURL($url, $method, $options);
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
