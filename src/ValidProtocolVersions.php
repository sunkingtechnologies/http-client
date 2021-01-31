<?php
/**
 * @author      Alagesan
 * @license     http://www.gnu.org/licenses/gpl-3.0.html
 * */

namespace SunKing;

trait ValidProtocolVersions
{
    protected static $validProtocolVersions = [
        '1.0' => true,
        '1.1' => true,
        '2.0' => true,
        '2'   => true,
    ];
}
