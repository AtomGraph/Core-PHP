<?php

// PHP rewrite of com.oreilly.servlet.multipart.ParamPart Java class
// JavaDoc: http://www.servlets.com/cos/javadoc/com/oreilly/servlet/multipart/ParamPart.html
// source code: http://www.java2s.com/Open-Source/Java-Document/Groupware/hipergate/com/oreilly/servlet/multipart/ParamPart.java.htm

namespace Graphity;

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
