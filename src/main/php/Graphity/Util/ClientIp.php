<?php
/**
 *  Helper class to retrieve Client IP address.
 *
 *  @author Julius Seporaitis <julius@graphity.org>
 */

namespace Graphity\Util;

class ClientIp
{
    /**
     * @return string
     */
    public static function asString()
    {
        if(getenv('HTTP_CLIENT_IP')) {
            $ip = getenv('HTTP_CLIENT_IP');
        }
        elseif(getenv('HTTP_X_FORWARDED_FOR')) {
            $ip = getenv('HTTP_X_FORWARDED_FOR');
        }
        elseif(getenv('HTTP_X_FORWARDED')) {
            $ip = getenv('HTTP_X_FORWARDED');
        }
        elseif(getenv('HTTP_FORWARDED_FOR')) {
            $ip = getenv('HTTP_FORWARDED_FOR');
        }
        elseif(getenv('HTTP_FORWARDED')) {
            $ip = getenv('HTTP_FORWARDED');
        } 
        elseif(getenv('REMOTE_ADDR')) {
            $ip = getenv('REMOTE_ADDR');
        }
        else {
            $ip = '';
        }
        
        return $ip;
    }

    /**
     * @return int
     */
    public static function asInt()
    {
        list(, $ip) = unpack('l', pack('l', ip2long(self::asString())));

        return $ip;
    }
}
