<?php

// PHP rewrite of com.oreilly.servlet.multipart.Part Java class
// JavaDoc: http://www.servlets.com/cos/javadoc/com/oreilly/servlet/multipart/Part.html
// source code: http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/multipart/Part.java.htm

namespace Graphity;

abstract class Part
{
    private $name = null;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function isFile()
    {
        return false;
    }

    public function isParam()
    {
        return false;
    }

}
