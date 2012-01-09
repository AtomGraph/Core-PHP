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
 *  PHP rewrite of com.oreilly.servlet.multipart.ParamPart Java class
 *  JavaDoc: http://www.servlets.com/cos/javadoc/com/oreilly/servlet/multipart/ParamPart.html
 *  source code: http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/multipart/ParamPart.java.htm
 */
class ParamPart extends Part
{
    const MAX_LENGTH = 131072;

    private $value = null;
    private $encoding = null;

    public function __construct($name, $in, $boundary, $encoding)
    {
        parent::__construct($name);
        $this->encoding = $encoding;

        /*
        // Copy the part's contents into a byte array
        PartInputStream pis = new PartInputStream(in, boundary);
        ByteArrayOutputStream baos = new ByteArrayOutputStream(512);
        byte[] buf = new byte[128];
        int read;
        while ((read = pis.read(buf)) != -1) {
            baos.write(buf, 0, read);
        }
        pis.close();
        baos.close();

        // save it for later
        value = baos.toByteArray();
        */
        $this->value = rtrim(stream_get_line($in, self::MAX_LENGTH, $boundary));
        stream_get_line($in, 1); stream_get_line($in, 1);  // strip possibly following \r\n
    }

    public function getValue()
    {
        return $this->value;
    }

    public function isParam()
    {
        return true;
    }
}
