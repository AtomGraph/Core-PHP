<?php
/**
 * Singleton interface to allow only a single instance of a class. Used by several classes such as Controller, Request, Response.
 *
 * @package		graphity
 * @author		Martynas Jusevicius <pumba@xml.lt>
 * @link		http://www.xml.lt
 */

namespace Graphity;

interface Singleton
{
    static function getInstance();
}

