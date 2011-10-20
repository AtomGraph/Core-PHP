<?php
/**
 * Represents a cookie.
 *
 * @package		    graphity
 * @author		    Martynas Jusevicius <pumba@xml.lt>
 * @link		    http://www.xml.lt
 */

namespace Graphity;

class Cookie
{

    /**
     * @var string $name   Cookie name
     */
    private $name = null;

    /**
     * @var string $path   Cookie value
     */
    private $path = null;

    /**
     * @var string $value  Cookie value
     */
    private $value = null;

    /**
     * @var string $expiry Expiry date & time
     */
    private $expiry = - 1;

    /**
     * Constructs a cookie with a specified name and value.
     *
     * @param string $name Name
     * @param string $value Value
     */
    public function __construct($name, $value)
    {
        $this->name = $name;
        $this->value = $value;
    }

    /**
     * Returns name of this Cookie.
     *
     * @return string Name
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Returns the path on the server to which the browser returns this Cookie (sub-directories included).
     *
     * @return string Path
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Specifies a path for the cookie to which the client should return the Cookie (sub-directories included).
     *
     * @param string $path Path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * Returns value of this Cookie.
     *
     * @return string Value
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Sets value of this Cookie.
     *
     * @param string $value Value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }

    /**
     * Returns the maximum age of the cookie in seconds.
     *
     * @return int An integer specifying the maximum age of the cookie in seconds; if negative, means the cookie persists until browser shutdown
     */
    public function getMaxAge()
    {
        return $this->expiry;
    }

    /**
     * Sets the maximum age of the cookie in seconds.
     *
     * @param int $expiry An integer specifying the maximum age of the cookie in seconds; if negative, means the cookie is not stored; if zero, deletes the cookie
     */
    public function setMaxAge($expiry)
    {
        $this->expiry = $expiry;
    }
}
