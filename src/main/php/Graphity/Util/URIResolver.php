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
 *  @author         Martynas Jusevičius <pumba@xml.lt>
 *  @link           http://graphity.org/
 */

namespace Graphity\Util;

use Graphity\Util\StringStream;
use Graphity\Exception;

/**
 * Serves a (XML) string from memory as a stream.
 * It is used to imitate files and pass dynamically generated side XML documents to XSLT, where they can be accessed using XPath's document() function, e.g. document('arg://books')
 */
class URIResolver
{
    public static $xslArgs = null;

    private $scheme = null;

    private $args = array();

    /**
     * Constructs CustomURIResolver from scheme prefix string (e.g. "arg" or "http").
     *
     * @param string $scheme Scheme prefix string
     */
    public function __construct($scheme)
    {
        $this->scheme = $scheme;
        if(! in_array($scheme, stream_get_wrappers())) {
            stream_wrapper_register($scheme, "Graphity\\Util\\StringStream");
            //or throw new Exception("Failed to register '" . $scheme . "'");
        }
    }

    /**
     * Sets XML string as an argument (side document) which is later passed to XSLT stylesheet.
     *
     * @param string $name Name of the argument
     * @param DOMDocument $doc XML document
     */
    public function setArgument($name, \DOMDocument $doc)
    {
        self::$xslArgs[$name] = $doc->saveXML(); // could be refactored more nicely?
    }
}
