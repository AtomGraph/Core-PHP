<?php
/**
 * Serves a (XML) string from memory as a stream.
 * It is used to imitate files and pass dynamically generated side XML documents to XSLT, where they can be accessed using XPath's document() function, e.g. document('arg://books')
 *
 * @package         graphity
 * @author          Martynas Jusevicius <pumba@xml.lt>
 */

namespace Graphity\Util;

use Graphity\Util\StringStream;

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
            stream_wrapper_register($scheme, "Graphity\\Util\\StringStream") or die("Failed to register '" . $scheme . "'");
        }
    }

    /**
     * Sets XML string as an argument (side document) which is later passed to XSLT stylesheet.
     *
     * @param string $name Name of the argument
     * @param string $xml XML string
     */
    public function setArgument($name, $xml)
    {
        self::$xslArgs[$name] = $xml;
    }
}
