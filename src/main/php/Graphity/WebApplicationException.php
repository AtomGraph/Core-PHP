<?php

// http://jsr311.java.net/nonav/javadoc/javax/ws/rs/WebApplicationException.html
// http://php.net/manual/en/language.exceptions.extending.php

namespace Graphity;


class WebApplicationException extends \Exception
{

    public function __construct($code, $message = "Web application exception")
    {
        parent::__construct($message, $code);
    }
}
