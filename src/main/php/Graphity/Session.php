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

namespace Graphity;

/**
 * Provides a way to identify a user across more than one page request or visit to a Web site and to store information about that user.
 * Wraps PHP's functions and global arrays to emulate Java's HttpSession interface.
 */
class Session implements SessionInterface
{

    private $id;

    private $attributes = array();

    /**
     * Constructs Session (starts it and gives it an ID).
     */
    public function __construct()
    {
        session_start();
        $this->id = session_id();
    }

    /**
     * Returns session ID.
     * @return int Session ID
     */
    public function getID()
    {
        return $this->id;
    }

    /**
     * Returns ...
     *
     * @return int Interval
     */
    public function getMaxInactiveInterval()
    {
    
    }

    /**
     * Sets ...
     *
     * @param int $interval Interval..
     */
    public function setMaxInactiveInterval($interval)
    {
    
    }

    /**
     * Returns a session attribute, or null if it does not exist.
     *
     * @param string $name Name of the attribute
     * @return mixed Attribute
     */
    public function getAttribute($name)
    {
        //if (isset($this->attributes[$name])) return $this->attributes[$name];
        //else return null;
        if(isset($_SESSION[$name]))
            return $_SESSION[$name];
        else
            return null;
    }

    /**
     * Sets a session attribute.
     *
     * @param string $name Name of the attribute
     * @param string $value Value of the attribute
     */
    public function setAttribute($name, $value)
    {
        $_SESSION[$name] = $value;
    }

    /**
     * Removed a session attribute.
     *
     * @param string $name Name of the attribute
     */
    public function removeAttribute($name)
    {
        unset($_SESSION[$name]);
    }

    /**
     * Invalidates the current session.
     */
    public function invalidate()
    {
        if(isset($_COOKIE[session_name()]))
            setcookie(session_name(), "", time() - 42000, '/');
        session_destroy();
    }
}
