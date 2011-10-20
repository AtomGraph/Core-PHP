<?php
/**
 * Helper class, used by URIResolver to stream strings.
 *
 * @package     graphity
 * @author      Martynas Jusevicius <pumba@xml.lt>
 * @link        http://www.xml.lt
 */

namespace Graphity\Util;

use Graphity\Util\URIResolver;

class StringStream
{
    var $position;

    var $xslArg;

    public function stream_eof()
    {
        return $this->position >= strlen($this->xslArg);
    }

    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $this->position = 0;
        $url = parse_url($path);
        $varname = $url['host'];
        $xslArgs = &URIResolver::$xslArgs;
        if(isset($xslArgs['/' . $varname]))
            $this->xslArg = &$xslArgs['/' . $varname];
        elseif(isset($xslArgs[$varname]))
            $this->xslArg = &$xslArgs[$varname];
        else
            return false;
        return true;
    }

    public function stream_read($count)
    {
        $ret = substr($this->xslArg, $this->position, $count);
        $this->position += strlen($ret);
        return $ret;
    }

    public function stream_tell()
    {
        return $this->position;
    }

    public function url_stat()
    {
        return array();
    }
}
