<?php

/**
 *  Copyright 2011 Graphity Team
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  @package        graphity
 *  @author         Martynas Jusevičius <martynas@graphity.org>
 *  @link           http://graphity.org/
 */

namespace Graphity\Util;

use Graphity\Util\URIResolver;

/**
 * Helper class, used by URIResolver to stream strings.
 */
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
