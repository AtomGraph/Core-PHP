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
 *  PHP rewrite of com.oreilly.servlet.multipart.Part Java class
 *  JavaDoc: http://www.servlets.com/cos/javadoc/com/oreilly/servlet/multipart/Part.html
 *  source code: http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/multipart/Part.java.htm
 */
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
