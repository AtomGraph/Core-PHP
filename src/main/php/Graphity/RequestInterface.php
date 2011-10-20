<?php

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
