<?php

namespace Graphity;

interface SessionInterface
{
    function getID();

    function getAttribute($name);

    function setAttribute($name, $value);

    function getMaxInactiveInterval();

    function setMaxInactiveInterval($interval);

    function invalidate();
}

