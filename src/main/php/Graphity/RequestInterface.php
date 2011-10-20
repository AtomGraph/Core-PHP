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

namespace Graphity;

interface RequestInterface
{

    function getAttribute($name);

    function setAttribute($name, $value);

    function getContentType();

    function addCookie(Cookie $cookie); // not in Java Servlet API

    function getCookies();

    function getParameter($name);

    function setParameter($name, $value); // not in Java Servlet API

    function getMethod();

    function setMethod($method); // not in Java Servlet API

    function getHeader($name);

    function setHeader($name, $value); // not in Java Servlet API

    function getSession();

    function getParameterMap();

    function getPathInfo();

    function setPathInfo($path);

    function getRequestURI();

    function getServerName();

    function setServerName($name); // not in Java Servlet API

    function getServerPort();

    function getQueryString();
	
	function getInputStream();
}

?>
